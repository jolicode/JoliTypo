// Tiny syntax highlighters for the result views, themed by the page's CSS (.tok-* classes).
// They only wrap the source in spans: the textContent of the output is the source, unchanged.

const escapeHtml = (text) => text.replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[c]);

const span = (type, text) => `<span class="tok-${type}">${escapeHtml(text)}</span>`;

const PHP_KEYWORDS = new Set([
    'abstract', 'array', 'as', 'break', 'case', 'catch', 'class', 'const', 'continue', 'default', 'do', 'echo', 'else',
    'elseif', 'enum', 'extends', 'false', 'final', 'finally', 'fn', 'for', 'foreach', 'function', 'if', 'implements',
    'include', 'include_once', 'instanceof', 'interface', 'match', 'namespace', 'new', 'null', 'print', 'private',
    'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait',
    'true', 'try', 'use', 'while', 'yield',
]);

const PHP_TOKENS = new RegExp([
    /(?<heredoc><<<'?(?<label>[A-Za-z_]\w*)'?\n[\s\S]*?\n[ \t]*\k<label>\b)/.source,
    /(?<open><\?php)/.source,
    /(?<comment>\/\/[^\n]*|#[^\n]*|\/\*[\s\S]*?\*\/)/.source,
    /(?<string>'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*")/.source,
    /(?<variable>\$\w+)/.source,
    /(?<name>\\?[A-Za-z_]\w*(?:\\[A-Za-z_]\w*)*)/.source,
    /(?<punct>->|::|=>|[()[\]{};,=.])/.source,
].join('|'), 'g');

export const highlightPhp = (code) => {
    let html = '';
    let last = 0;
    for (const match of code.matchAll(PHP_TOKENS)) {
        html += escapeHtml(code.slice(last, match.index));
        last = match.index + match[0].length;

        const { heredoc, open, comment, string, variable, name, punct } = match.groups;
        if (heredoc) {
            // The nowdoc body is the user's content: markers and label as keywords, body as a string
            const [, head, body, tail] = heredoc.match(/^(<<<'?\w+'?\n)([\s\S]*?\n)([ \t]*\w+)$/);
            html += span('keyword', head) + span('string', body) + span('keyword', tail);
        } else if (open) {
            html += span('keyword', open);
        } else if (comment) {
            html += span('comment', comment);
        } else if (string) {
            html += span('string', string);
        } else if (variable) {
            html += span('variable', variable);
        } else if (name) {
            const previous = code.slice(0, match.index);
            const next = code.slice(last);
            if (PHP_KEYWORDS.has(name.toLowerCase())) {
                html += span('keyword', name);
            } else if (/->\s*$/.test(previous) || /^\s*\(/.test(next)) {
                html += span('function', name);
            } else {
                html += span('class', name);
            }
        } else {
            html += span('punct', punct);
        }
    }

    return html + escapeHtml(code.slice(last));
};

// The characters the fixers insert and that a browser does not show, as raw characters or as entities
const INVISIBLES = [
    { type: 'nbsp', chars: '\u00a0', entities: /^&(?:nbsp|#160|#x0*a0);$/i },
    { type: 'nnbsp', chars: '\u202f', entities: /^&(?:#8239|#x0*202f);$/i },
    { type: 'shy', chars: '\u00ad', entities: /^&(?:shy|#173|#x0*ad);$/i },
];

const invisibleOf = (text) => INVISIBLES.find(({ chars, entities }) => text === chars || entities.test(text));

const HTML_TOKENS = /(?<comment><!--[\s\S]*?-->)|(?<tag><\/?[A-Za-z][\w:-]*)(?<attrs>(?:\s+[^\s=>\/]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+))?)*)\s*(?<close>\/?>)/g;

const highlightAttributes = (attrs) => attrs.replace(/([^\s=]+)(\s*=\s*)?("[^"]*"|'[^']*'|[^\s>]+)?/g, (all, name, equal, value) => {
    if (/^\s+$/.test(all)) {
        return all;
    }

    return span('attr', name) + (equal ? span('punct', equal) : '') + (value ? span('string', value) : '');
});

export const highlightHtml = (code) => {
    let html = '';
    let last = 0;
    for (const match of code.matchAll(HTML_TOKENS)) {
        html += markText(code.slice(last, match.index));
        last = match.index + match[0].length;

        const { comment, tag, attrs, close } = match.groups;
        if (comment) {
            html += span('comment', comment);
        } else {
            const space = match[0].slice(tag.length + attrs.length, match[0].length - close.length);
            html += span('tag', tag) + highlightAttributes(attrs) + space + span('tag', close);
        }
    }

    return html + markText(code.slice(last));
};

// Escapes a text node of the HTML source and marks its entities and invisible characters
const markText = (text) => {
    let html = '';
    let last = 0;
    for (const match of text.matchAll(/[\u00a0\u202f\u00ad]|&(?:#x?[0-9a-f]+|[a-z]+\d*);/gi)) {
        html += escapeHtml(text.slice(last, match.index));
        last = match.index + match[0].length;

        const found = match[0];
        const token = found.startsWith('&') ? span('entity', found) : escapeHtml(found);
        const invisible = invisibleOf(found);
        html += invisible ? `<span class="ws ws--${invisible.type}" title="${TITLES[invisible.type]}">${token}</span>` : token;
    }

    return html + escapeHtml(text.slice(last));
};

const TITLES = { nbsp: 'No-break space', nnbsp: 'Narrow no-break space', shy: 'Soft hyphen' };
