import pdfplumber
import json
import re
import os

PDF_PATH = r'E:\Codigo-2025-GAM-ESP.pdf'
OUTPUT_PATH = os.path.join(os.path.dirname(__file__), '..', 'reglas_minadas.json')

APARATOS = {
    'Suelo': 1,
    'Caballo con Arzones': 2,
    'Anillas': 3,
    'Salto': 4,
    'Barras Paralelas': 5,
    'Barra Fija': 6,
}

DEDUCTION_PAGES = [
    (26, 'Suelo'),   # both Jurado D + E tables
    (45, 'Caballo con Arzones'),  # Jurado E
    (61, 'Anillas'),  # both Jurado D + E
    (82, 'Salto'),   # both Jurado D + E
    (94, 'Barras Paralelas'),  # both Jurado D + E
    (116, 'Barra Fija'),  # Jurado E
]


def clean_text(text):
    if not text:
        return ''
    text = text.replace('\n', ' ').replace('\r', '')
    text = re.sub(r'-\s+', '', text)  # remove hyphen+space from line breaks
    text = re.sub(r'\s+', ' ', text).strip()
    return text


def extract_numeric_value(text):
    if not text:
        return None
    match = re.search(r'(\d+)[,.](\d+)', text)
    if match:
        return float(f"{match.group(1)}.{match.group(2)}")
    return None


def detect_jurado_type(page_text, table_index, page_num):
    """Detect if a table corresponds to Jurado D or Jurado E."""
    if 'Jurado D' in page_text and 'Jurado E' in page_text:
        # Find positions in text
        pos_d = page_text.index('Jurado D')
        pos_e = page_text.index('Jurado E')
        # Simple heuristic: first table with "Falta" header after Jurado D is D,
        # after Jurado E is E
        if pos_d < pos_e:
            if table_index == 0:
                return 'D'
            else:
                # Check if there's a separate Jurado E table
                return 'E'
        else:
            if table_index == 0:
                return 'E'
            else:
                return 'D'
    elif 'Jurado D' in page_text:
        return 'D'
    elif 'Jurado E' in page_text:
        return 'E'
    return None


def extract_deductions():
    rules = []
    
    with pdfplumber.open(PDF_PATH) as pdf:
        for page_num, aparato in DEDUCTION_PAGES:
            page = pdf.pages[page_num]
            page_full_text = page.extract_text() or ''
            tables = page.extract_tables()
            
            jurado_d_found = False
            jurado_e_found = False
            
            for ti, table in enumerate(tables):
                if len(table) < 2:
                    continue
                
                header_row = table[0]
                if not header_row or not any(h and 'Falta' in str(h) for h in header_row):
                    continue
                
                # Assign tipo based on table index and context
                has_d = 'Jurado D' in page_full_text
                has_e = 'Jurado E' in page_full_text
                
                if has_d and has_e:
                    pos_d = page_full_text.index('Jurado D')
                    pos_e = page_full_text.index('Jurado E')
                    # The table's approximate position in text = its row content
                    first_fault = clean_text(str(table[1][0])) if len(table) > 1 else ''
                    # Find where in the page text this fault appears
                    fault_pos = page_full_text.find(first_fault[:30]) if first_fault else -1
                    
                    if fault_pos >= 0:
                        if fault_pos < pos_e:
                            tipo = 'D'
                        else:
                            tipo = 'E'
                    else:
                        # Fallback: first table D, second E
                        tipo = 'D' if ti == 0 else 'E'
                elif has_d:
                    tipo = 'D'
                elif has_e:
                    tipo = 'E'
                else:
                    continue
                
                col_map = {}
                for ci, h in enumerate(header_row):
                    if h:
                        hstr = str(h)
                        if '0.10' in hstr:
                            col_map[ci] = 0.10
                        elif '0.30' in hstr:
                            col_map[ci] = 0.30
                        elif '0.50' in hstr:
                            col_map[ci] = 0.50
                
                for row in table[1:]:
                    if not row or not row[0] or not str(row[0]).strip():
                        continue
                    
                    fault_text = clean_text(str(row[0]))
                    if not fault_text or fault_text in ('Falta',):
                        continue
                    if re.match(r'^[A-Za-z]\s*=', fault_text) or re.match(r'^EG\s', fault_text):
                        continue
                    if re.match(r'^N[°º]', fault_text):
                        continue
                    
                    if not col_map:
                        # Direct-format table: Falta | Deducción
                        if len(row) >= 2 and row[1] and clean_text(str(row[1])):
                            val_text = clean_text(str(row[1]))
                            val = extract_numeric_value(val_text)
                            if val is not None:
                                rules.append({
                                    'aparato': aparato,
                                    'ID_Aparato': APARATOS[aparato],
                                    'tipo': tipo,
                                    'descripcion': fault_text,
                                    'valor': val,
                                    'criterios': val_text if val_text != str(val) else None
                                })
                        continue
                    
                    entries = []
                    for ci, cell in enumerate(row[1:], start=1):
                        dv = col_map.get(ci)
                        if dv is None:
                            continue
                        cell_text = str(cell).strip() if cell else ''
                        if cell_text == '+':
                            entries.append((dv, None))
                        elif cell_text:
                            ev = extract_numeric_value(cell_text)
                            if ev is not None:
                                entries.append((ev, cell_text))
                            else:
                                entries.append((dv, cell_text))
                    
                    if entries:
                        for val, crit in entries:
                            rules.append({
                                'aparato': aparato,
                                'ID_Aparato': APARATOS[aparato],
                                'tipo': tipo,
                                'descripcion': fault_text,
                                'valor': val,
                                'criterios': crit
                            })
    
    # Deduplicate
    seen = set()
    deduped = []
    for r in rules:
        key = (r['ID_Aparato'], r['tipo'], r['descripcion'], r['valor'], str(r.get('criterios', '')))
        if key not in seen:
            seen.add(key)
            deduped.append(r)
    
    return deduped


def main():
    print("Extrayendo reglas de deduccion del Codigo FIG 2025-2028...")
    rules = extract_deductions()
    
    grouped = {}
    for r in rules:
        key = r['ID_Aparato']
        if key not in grouped:
            grouped[key] = []
        grouped[key].append(r)
    
    output = {
        'metadata': {
            'source': 'Codigo de Puntuacion FIG 2025-2028 (GAM)',
            'total_rules': len(rules),
        },
        'rules': rules,
    }
    
    with open(OUTPUT_PATH, 'w', encoding='utf-8') as f:
        json.dump(output, f, ensure_ascii=False, indent=2)
    
    print(f"\nExtraccion completa!")
    print(f"Total reglas extraidas: {len(rules)}")
    for aparato_name, aid in APARATOS.items():
        count = len(grouped.get(aid, []))
        print(f"  {aparato_name}: {count} reglas")
    print(f"\nArchivo guardado: {OUTPUT_PATH}")


if __name__ == '__main__':
    main()
