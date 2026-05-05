<?php

use App\Models\Raiida\VocabularyItem;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = VocabularyItem::where('period', 'P4')
    ->where(function($q) {
        $q->whereNull('lexical_type')->orWhere('lexical_type', '');
    })->get();

$count = 0;
foreach ($items as $item) {
    $word = trim($item->word);
    $wordLower = mb_strtolower($word, 'UTF-8');
    
    $type = null;
    $gender = null;
    
    // Check articles for Nouns
    if (preg_match('/^(le |un |du |au )/i', $word) || 
        preg_match('/^(l\'|l’)/i', $word) && preg_match('/(air|exercice|arbre|ours|hippopotame|enclos|ordinateur|hôpital|hôtel)/i', $word)) {
        $type = 'nom';
        $gender = 'masculine';
    } elseif (preg_match('/^(la |une |de la )/i', $word) || 
              preg_match('/^(l\'|l’)/i', $word) && preg_match('/(histoire|intelligence|eau|école|église|équipe|étagère|île)/i', $word)) {
        $type = 'nom';
        $gender = 'feminine';
    } elseif (preg_match('/^(les |des |aux )/i', $word)) {
        $type = 'nom';
        // Check singular equivalent or default
        if (preg_match('/(billes|chaussettes|crêpes|glaces|lunettes|sardines|babouches)/i', $word)) {
            $gender = 'feminine';
        } else {
            $gender = 'masculine';
        }
    } 
    // Check verbs (infinitive or common conjugated forms)
    elseif (preg_match('/(er|ir|re|oir)$/i', $word) && !preg_match('/^(le |la |un |une |les |des |l\')/i', $word)) {
        $type = 'verbe';
    } 
    // Expressions / Phrases / Adverbs / Adjectives
    elseif (preg_match('/^(à |en |de |avec |pour |sans |sous |sur |dans )/i', $word)) {
        $type = 'expression';
    } elseif (preg_match('/(ment)$/i', $word)) {
        $type = 'adverbe';
    } else {
        $type = 'adjectif'; // Default fallback for single words like Agréable, Extraordinaire
    }
    
    // Assign generic distractor group
    $group = 'divers';
    if (preg_match('/(petit-déjeuner|goûter|dîner|crêpes|glaces|radis|sardines|eau|manger|boire)/i', $word)) {
        $group = 'nourriture';
    } elseif (preg_match('/(ours|souris|canard|nid|hippopotame|animal)/i', $word)) {
        $group = 'animaux';
    } elseif (preg_match('/(habiller|pyjama|chemise|chaussettes|babouches|laver)/i', $word)) {
        $group = 'vêtements_et_hygiène';
    } elseif (preg_match('/(école|leçon|exercice|histoire|cahier|stylo|lire|écrire|calculer)/i', $word)) {
        $group = 'école';
    } elseif (preg_match('/(jouer|billes|jeux|cache-cache|gagner)/i', $word)) {
        $group = 'jeux';
    }

    $item->lexical_type = $type;
    if ($type === 'nom') {
        $item->gender = $gender;
    }
    $item->distractor_group = $group;
    $item->save();
    $count++;
}

echo "Successfully classified $count items locally without API!\n";
