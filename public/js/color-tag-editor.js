if (typeof window.colorTagEditor === 'undefined') {
    window.colorTagEditor = function (config) {
        return {
            statePath: config.statePath,
            colorStyles: {
                'BLUE': 'color: #2E76B6; font-weight: 600;',
                'RED': 'color: #AF0A54; font-weight: 600;',
                'GREEN': 'color: #00AAA4; font-weight: 600;',
                'PINK': 'color: #DC03A2; font-weight: 600;',
                'YELLOW': 'color: #ca8a04; font-weight: 600;',
                'PURPLE': 'color: #9333ea; font-weight: 600;',
                'ORANGE': 'color: #f97316; font-weight: 600;',
            },

            tagsToHtml(text) {
                if (!text) return '';
                let result = text;
                result = result.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                result = result.replace(/\[(BLUE|RED|GREEN|PINK|YELLOW|PURPLE|ORANGE)\](.*?)\[\/\1\]/gi, (match, color, content) => {
                    const style = this.colorStyles[color.toUpperCase()] || '';
                    return '<span data-color="' + color.toUpperCase() + '" style="' + style + '">' + content + '</span>';
                });
                return result;
            },

            htmlToTags(element) {
                if (!element) return '';
                const clone = element.cloneNode(true);
                clone.querySelectorAll('span[data-color]').forEach(span => {
                    const color = span.getAttribute('data-color');
                    const text = span.textContent;
                    const textNode = document.createTextNode('[' + color + ']' + text + '[/' + color + ']');
                    span.parentNode.replaceChild(textNode, span);
                });
                return clone.textContent || clone.innerText || '';
            },

            setup() {
                const hydrate = () => {
                    if (!this.$refs.editor) return;
                    const initialValue = this.$wire.get(this.statePath) || '';
                    this.$refs.editor.innerHTML = this.tagsToHtml(initialValue);
                };

                this.$nextTick(() => {
                    hydrate();
                    setTimeout(() => hydrate(), 100);
                    setTimeout(() => hydrate(), 300);
                });

                this.$watch('$wire.' + this.statePath, (value) => {
                    if (!this.$refs.editor) return;
                    const currentTags = this.htmlToTags(this.$refs.editor);
                    if (value !== currentTags) {
                        this.$refs.editor.innerHTML = this.tagsToHtml(value || '');
                    }
                });
            },

            syncToLivewire() {
                if (this.$refs.editor) {
                    const tagValue = this.htmlToTags(this.$refs.editor);
                    this.$wire.set(this.statePath, tagValue);
                }
            },

            onInput() {
                this.syncToLivewire();
            },

            applyColor(color) {
                const selection = window.getSelection();
                if (!selection.rangeCount) return;
                const range = selection.getRangeAt(0);
                const selectedText = range.toString();
                if (!selectedText) return;
                if (!this.$refs.editor.contains(range.commonAncestorContainer)) return;

                const span = document.createElement('span');
                span.setAttribute('data-color', color);
                span.style.cssText = this.colorStyles[color] || '';
                span.textContent = selectedText;

                range.deleteContents();
                range.insertNode(span);
                range.setStartAfter(span);
                range.setEndAfter(span);
                selection.removeAllRanges();
                selection.addRange(range);
                this.syncToLivewire();
            },

            removeColor() {
                if (this.$refs.editor) {
                    const spans = this.$refs.editor.querySelectorAll('span[data-color]');
                    spans.forEach(span => {
                        const text = document.createTextNode(span.textContent);
                        span.parentNode.replaceChild(text, span);
                    });
                    this.syncToLivewire();
                }
            },

            onPaste(e) {
                e.preventDefault();
                const text = e.clipboardData.getData('text/plain');
                document.execCommand('insertText', false, text);
            },

            onKeydown(e) {
                if ((e.ctrlKey || e.metaKey) && ['b', 'i', 'u'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                }
            }
        };
    };
}
