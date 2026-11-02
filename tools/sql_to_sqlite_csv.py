import re, sqlite3, sys, csv, os
src, dbp, csvdir = sys.argv[1:4]
txt = open(src, encoding='utf-8').read()
KEEP = re.compile(r'test17\w*$')
if os.path.exists(dbp): os.remove(dbp)
con = sqlite3.connect(dbp)
# create tables
for m in re.finditer(r'CREATE TABLE `(\w+)` \((.*?)\n\) ENGINE', txt, re.S):
    t, body = m.groups()
    if not KEEP.match(t): continue
    cols = [re.match(r'\s*`(\w+)`\s+(\w+)', l).groups() for l in body.strip().split(',\n')]
    ty = lambda s: 'INTEGER' if s=='int' else 'TEXT'
    con.execute(f'CREATE TABLE {t} (' + ', '.join(f'{c} {ty(s)}' for c,s in cols) + ')')
def parse_values(s, i):
    rows=[]; n=len(s)
    while i<n:
        while s[i] in ' ,\n\r': i+=1
        if s[i]==';': return rows, i
        assert s[i]=='(', s[i-20:i+20]; i+=1; row=[]
        while True:
            while s[i]==' ': i+=1
            if s[i]=="'":
                i+=1; buf=[]
                while True:
                    c=s[i]
                    if c=='\\':
                        e=s[i+1]; buf.append({'n':'\n','r':'\r','t':'\t','0':'\0','Z':'\x1a'}.get(e,e)); i+=2
                    elif c=="'":
                        if s[i+1]=="'": buf.append("'"); i+=2
                        else: i+=1; break
                    else: buf.append(c); i+=1
                row.append(''.join(buf))
            else:
                j=i
                while s[j] not in ',)': j+=1
                tok=s[i:j].strip(); i=j
                row.append(None if tok=='NULL' else (int(tok) if re.fullmatch(r'-?\d+',tok) else float(tok)))
            while s[i]==' ': i+=1
            if s[i]==',': i+=1; continue
            assert s[i]==')'; i+=1; break
        rows.append(row)
for m in re.finditer(r'INSERT INTO `(\w+)` \([^)]*\) VALUES\n', txt):
    t=m.group(1)
    if not KEEP.match(t): continue
    rows,_=parse_values(txt, m.end())
    con.executemany(f'INSERT INTO {t} VALUES ('+','.join('?'*len(rows[0]))+')', rows)
con.commit()
os.makedirs(csvdir, exist_ok=True)
for (t,) in con.execute("select name from sqlite_master where type='table' order by name"):
    cur=con.execute(f'select * from {t}')
    with open(f'{csvdir}/{t}.csv','w',newline='',encoding='utf-8') as f:
        w=csv.writer(f); w.writerow([d[0] for d in cur.description]); w.writerows(cur)
    print(t, con.execute(f'select count(*) from {t}').fetchone()[0])
