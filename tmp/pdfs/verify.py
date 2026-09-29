from pathlib import Path
import sqlite3, re, math
p=Path('output/corriges-mysql/Corriges_exercices_MySQL.txt')
text=p.read_text(encoding='utf-8')
db=sqlite3.connect(':memory:')
db.create_function('SQRT',1,math.sqrt)
db.create_function('CHAR_LENGTH',1,len)
db.create_function('CONCAT',-1,lambda *xs: ''.join(map(str,xs)))
db.execute('CREATE TABLE users(id_user INTEGER PRIMARY KEY, firstname TEXT, lastname TEXT, gender TEXT, date_of_birth TEXT, city TEXT, weight_kg INTEGER)')
db.executemany('INSERT INTO users VALUES(?,?,?,?,?,?,?)',[
(1,'James','Bond','M','2007-07-07','London',70),
(2,'Jack','Bauer','M','2004-12-24','New-York',72),
(3,'Lara','Croft','F','2000-08-01','Washington',73),
(9,'Beyonce','Knowles','F','1981-09-04','Houston',70),
(10,'Alice','Martin','F','1995-03-12','Bruxelles',58),
(11,'Bruno','Durand','M','1988-07-22','Anvers',92),
(12,'Charlie','Petit','X','2002-11-05','Bruxelles',67),
(13,'Alex','Leroy','X','2005-04-18','Anvers',81)])
queries=re.findall(r'^SELECT\b[\s\S]*?;',text,re.M)
for q in queries:
    db.execute(q).fetchall()
assert db.execute("SELECT REPLACE('London','don','dres')").fetchone()[0]=='Londres'
assert db.execute("SELECT REPLACE('Bruxelles','xelle','ssel')").fetchone()[0]=='Brussels'
assert db.execute('SELECT id_user FROM users ORDER BY id_user LIMIT 2,2').fetchall()==[(3,),(9,)]
assert len(re.findall(r'^Exercice \d+ -',text,re.M))==68
print(f'{len(queries)} requêtes SELECT vérifiées sur des données fictives avec SQLite et fonctions équivalentes ; DDL MySQL non exécuté.')
print('68 points numérotés couverts, dont un rappel de prérequis. Remplacements et pagination vérifiés.')
# Format Windows, UTF-8 BOM for Notepad.
p.write_bytes(b'\xef\xbb\xbf'+text.replace('\r\n','\n').replace('\n','\r\n').encode('utf-8'))
