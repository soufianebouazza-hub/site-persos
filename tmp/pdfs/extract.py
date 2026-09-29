from pathlib import Path
from pypdf import PdfReader
root=Path(r'C:\Users\soufi\Desktop\cours_cfitch\mysql')
out=Path('tmp/pdfs')
for path in root.glob('*.pdf'):
    reader=PdfReader(path)
    text='\n\n'.join(f'--- PAGE {i+1} ---\n'+(p.extract_text() or '') for i,p in enumerate(reader.pages))
    (out/(path.stem+'.txt')).write_text(text,encoding='utf-8')
    print(path.name, len(reader.pages), 'pages', len(text), 'characters')
