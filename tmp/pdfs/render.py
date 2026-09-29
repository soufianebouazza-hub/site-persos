from pathlib import Path
import pypdfium2 as pdfium
root=Path(r'C:\Users\soufi\Desktop\cours_cfitch\mysql')
for name, pages in [('MySQL part 2.pdf',[14]),('MySQL part 3.pdf',[1]),('Cours mysql 24 septembre.pdf',[4])]:
    doc=pdfium.PdfDocument(str(root/name))
    for page in pages:
        dest=Path('tmp/pdfs')/(Path(name).stem+f'-p{page}.png')
        doc[page-1].render(scale=1.7).to_pil().save(dest)
        print(dest)
