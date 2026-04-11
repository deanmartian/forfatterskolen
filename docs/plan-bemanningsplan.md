# Bemanningsplan — Prosjektplan

## Problemet
Bemanningen av kurs (spesielt Årskurs) styres via Excel-ark utenfor systemet. Redaktører vet ikke hvem de skal følge opp, og admin har ingen oversikt i portalen.

## Løsning
Ny bemanningsplan-modul synlig i:
- **Admin**: Full oversikt + tildeling per kurs
- **Redaktørportalen**: Redaktøren ser sine tildelte elever, frister og oppgaver

## Funksjoner

### 1. Bemanningsplan per kurs (admin)
- Oversikt: alle elever på kurset + tildelt redaktør
- Drag & drop eller dropdown for å tildele redaktør til elev
- Tildel kursholder per webinar
- Gjesteredaktører med dato-intervall
- Mentor-tildeling
- Kapasitetsoversikt: antall elever per redaktør

### 2. Redaktørportalen
- "Mine elever" viser kun elever tildelt meg
- Frister: når neste innlevering er, når jeg må levere tilbakemelding
- Kalender-visning med alle mine frister
- Varsler når ny innlevering kommer inn

### 3. Database
```
course_staff (ny tabell):
- id
- course_id (FK)
- user_id (FK → redaktør/mentor)
- role: editor, mentor, guest_editor, course_leader, webinar_host
- student_user_id (FK → elev, nullable for kursroller)
- webinar_id (FK, nullable for webinar-host)
- start_date, end_date (for gjesteredaktører)
- notes
- created_at, updated_at
```

### 4. Admin-sider
- `/admin/course/{id}/staff` — bemanningsplan for kurset
- Tabell: Elev | Redaktør | Oppgave 1 status | Oppgave 2 status | ...
- Bulk-tildeling: "Tildel alle uten redaktør til Annina"

### 5. Redaktørportal-sider
- `/mine-elever` oppdatert med bemanningsplan-data
- Filtrer på kurs
- Se frister for tilbakemelding

## Prioritert rekkefølge
1. Migrering + modell (course_staff)
2. Admin-side for tildeling
3. Redaktørportal-visning
4. Kapasitetsoversikt
5. Varsler/påminnelser
