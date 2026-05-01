#!/usr/bin/env python3
"""
Compute question counts and how many published questions use distractors
from a different SEM (period/week) than the target concept.
"""

from __future__ import annotations

import argparse
import json
import re
import sqlite3
from collections import defaultdict
from pathlib import Path
from typing import Any


COLOR_TAGS_PATTERN = re.compile(r"\[/?(?:BLUE|PINK|RED|GREEN|YELLOW|PURPLE|ORANGE)\]", re.IGNORECASE)


def normalize_text(text: str) -> str:
    text = COLOR_TAGS_PATTERN.sub("", text or "")
    text = text.replace("’", "'").strip().lower()
    return re.sub(r"\s+", " ", text)


def load_rows(conn: sqlite3.Connection, query: str) -> list[sqlite3.Row]:
    cursor = conn.cursor()
    return cursor.execute(query).fetchall()


def compute_stats(db_path: Path) -> dict[str, Any]:
    conn = sqlite3.connect(str(db_path))
    conn.row_factory = sqlite3.Row

    vocab_rows = load_rows(
        conn,
        """
        SELECT id, concept_id, word, grade, period, week, revizy_image_file_id, revizy_audio_file_id
        FROM vocabulary_items
        """,
    )
    attempts = load_rows(
        conn,
        """
        SELECT id, concept_id, status, question_data
        FROM question_publish_attempts
        """,
    )

    concept_target: dict[str, dict[str, Any]] = {}
    by_image: dict[str, list[dict[str, Any]]] = defaultdict(list)
    by_audio: dict[str, list[dict[str, Any]]] = defaultdict(list)
    by_word: dict[str, list[dict[str, Any]]] = defaultdict(list)

    for row in vocab_rows:
        item = dict(row)
        concept_id = str(item.get("concept_id") or "").strip()
        if concept_id:
            if concept_id not in concept_target or int(item["id"]) < int(concept_target[concept_id]["id"]):
                concept_target[concept_id] = item

        image_id = str(item.get("revizy_image_file_id") or "").strip()
        audio_id = str(item.get("revizy_audio_file_id") or "").strip()
        word = str(item.get("word") or "").strip()

        if image_id:
            by_image[image_id].append(item)
        if audio_id:
            by_audio[audio_id].append(item)
        if word:
            by_word[normalize_text(word)].append(item)

    published = [a for a in attempts if str(a["status"] or "").lower() == "published"]

    resolved_with_distractor_map = 0
    not_same_sem = 0
    same_sem = 0
    no_target = 0
    no_distractor_or_unmappable = 0
    invalid_question_json = 0

    for attempt in published:
        concept_id = str(attempt["concept_id"] or "").strip()
        target = concept_target.get(concept_id)
        if not target:
            no_target += 1
            continue

        target_grade = str(target.get("grade") or "")
        target_period = str(target.get("period") or "").upper()
        target_week = str(target.get("week") or "").upper()

        raw = attempt["question_data"] or ""
        try:
            question_data = json.loads(raw) if raw else {}
        except json.JSONDecodeError:
            invalid_question_json += 1
            continue

        answers = question_data.get("answers") if isinstance(question_data, dict) else None
        if not isinstance(answers, list):
            no_distractor_or_unmappable += 1
            continue

        mapped_distractors: list[dict[str, Any]] = []
        for answer in answers:
            if not isinstance(answer, dict):
                continue
            if answer.get("is_correct") is True:
                continue

            candidates: list[dict[str, Any]] = []
            media = answer.get("media")
            if isinstance(media, dict):
                image_id = str(media.get("image") or "").strip()
                audio_id = str(media.get("audio") or "").strip()
                if image_id:
                    candidates.extend(by_image.get(image_id, []))
                if audio_id:
                    candidates.extend(by_audio.get(audio_id, []))

            body = answer.get("body")
            if isinstance(body, str) and body.strip():
                candidates.extend(by_word.get(normalize_text(body), []))

            if not candidates:
                continue

            same_grade = [c for c in candidates if str(c.get("grade") or "") == target_grade]
            if same_grade:
                candidates = same_grade

            mapped_distractors.append(candidates[0])

        if not mapped_distractors:
            no_distractor_or_unmappable += 1
            continue

        resolved_with_distractor_map += 1
        has_cross_sem = any(
            str(d.get("period") or "").upper() != target_period
            or str(d.get("week") or "").upper() != target_week
            for d in mapped_distractors
        )

        if has_cross_sem:
            not_same_sem += 1
        else:
            same_sem += 1

    conn.close()

    not_same_sem_pct = (not_same_sem / resolved_with_distractor_map * 100.0) if resolved_with_distractor_map else 0.0

    return {
        "all_attempts": len(attempts),
        "published_attempts": len(published),
        "resolved_with_distractor_map": resolved_with_distractor_map,
        "not_same_sem": not_same_sem,
        "same_sem": same_sem,
        "not_same_sem_pct_on_resolved": round(not_same_sem_pct, 2),
        "no_target": no_target,
        "no_distractor_or_unmappable": no_distractor_or_unmappable,
        "invalid_question_json": invalid_question_json,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description="Question SEM statistics from Seeder SQLite DB.")
    parser.add_argument(
        "--db",
        default="Seeder/database/database.sqlite",
        help="Path to SQLite DB (default: Seeder/database/database.sqlite)",
    )
    args = parser.parse_args()

    db_path = Path(args.db).expanduser().resolve()
    if not db_path.exists():
        print(f"Database not found: {db_path}")
        return 1

    stats = compute_stats(db_path)
    print(json.dumps(stats, indent=2, ensure_ascii=True))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
