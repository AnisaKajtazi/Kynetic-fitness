from pathlib import Path

root = Path('src')
replace_map = {
    '#2563eb': 'var(--accent-blue)',
    '#1e90ff': 'var(--accent-blue)',
    '#10b981': 'var(--accent-lavender)',
    '#ef4444': 'var(--accent-plum)',
    '#dc2626': 'var(--accent-plum)',
    '#b91c1c': 'var(--accent-plum)',
    '#ff5722': 'var(--accent-lavender)',
    '#e3342f': 'var(--accent-plum)',
    '#d4af37': 'var(--accent-blue)',
    '#f0c75e': 'var(--accent-blue)',
    '#1a73e8': 'var(--accent-blue)',
    '#155ab6': 'var(--accent-lavender)',
    '#1f2937': 'var(--bg-card)',
    '#111827': 'var(--bg-card)',
    '#0f1115': 'var(--bg-card)',
    '#1c1f26': 'var(--bg-card)',
    'color: #fff': 'color: var(--text-strong)',
    'color: #ffffff': 'color: var(--text-strong)',
    'background: #fff': 'background: var(--bg-card)',
    'background: #ffffff': 'background: var(--bg-card)',
    'background-color: #fff': 'background-color: var(--bg-card)',
    'background-color: #ffffff': 'background-color: var(--bg-card)',
    'border-color: #1a73e8': 'border-color: var(--accent-blue)',
    'border-color: #2563eb': 'border-color: var(--accent-blue)',
    'border: 3px solid #2563eb': 'border: 3px solid var(--accent-blue)',
    'box-shadow: 0 0 15px #2563eb': 'box-shadow: 0 0 15px rgba(var(--theme-ice-rgb), 0.55)',
    'box-shadow: 0 0 0 2px #2563eb': 'box-shadow: 0 0 0 2px rgba(var(--theme-ice-rgb), 0.45)',
    'background: linear-gradient(90deg, #d4af37, #f0c75e);': 'background: linear-gradient(90deg, var(--accent-blue), var(--accent-lavender));',
    'background: linear-gradient(135deg, #1f2937, #111827);': 'background: linear-gradient(145deg, var(--bg-card), rgba(var(--theme-night-rgb), 0.96));',
    'background: linear-gradient(145deg, #1f2937, #111827);': 'background: linear-gradient(145deg, var(--bg-card), rgba(var(--theme-night-rgb), 0.96));',
    'background: linear-gradient(135deg, #0f1115, #1c1f26);': 'background: linear-gradient(145deg, var(--bg-card), rgba(var(--theme-night-rgb), 0.96));',
    'background: linear-gradient(90deg, #d4af37, #f0c75e);': 'background: linear-gradient(90deg, var(--accent-blue), var(--accent-lavender));',
    'color: #1e90ff': 'color: var(--accent-blue)',
    'border-top: 1px solid #1f2937': 'border-top: 1px solid var(--bg-card)',
    'background: #1a73e8': 'background: var(--accent-blue)',
    'background: #155ab6': 'background: var(--accent-lavender)',
    'background: #2563eb': 'background: var(--accent-blue)',
    'background: #ef4444': 'background: var(--accent-plum)',
    'background: #10b981': 'background: var(--accent-lavender)',
    'background: #1f2937': 'background: var(--bg-card)',
    'background: #111827': 'background: var(--bg-card)',
    'background: #0f1115': 'background: var(--bg-card)',
    'background: #1c1f26': 'background: var(--bg-card)',
}

for pattern, replacement in replace_map.items():
    print(f'Replacing {pattern} -> {replacement}')

for path in root.rglob('*.vue'):
    text = path.read_text(encoding='utf-8')
    original = text
    for pattern, replacement in replace_map.items():
        text = text.replace(pattern, replacement)
    if text != original:
        path.write_text(text, encoding='utf-8')

for path in root.rglob('*.css'):
    text = path.read_text(encoding='utf-8')
    original = text
    for pattern, replacement in replace_map.items():
        text = text.replace(pattern, replacement)
    if text != original:
        path.write_text(text, encoding='utf-8')

print('Done replacements')
