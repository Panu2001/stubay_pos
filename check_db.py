import sqlite3
import json

conn = sqlite3.connect('database/database.sqlite')
conn.row_factory = sqlite3.Row
cur = conn.cursor()

cur.execute("SELECT * FROM supplier_purchases")
rows = cur.fetchall()

result = [dict(row) for row in rows]
with open('scratch_output.json', 'w') as f:
    json.dump(result, f, indent=4)

print("Done")
