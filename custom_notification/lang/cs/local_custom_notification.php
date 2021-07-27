<?php
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/****
 * 
 * Language EN
 * 
 * 
 * @package local_manager
 * @copyrigh 2021 Simon
 * @licence GNU
 * 
 */

 $string['custom_notification'] = 'Vlastní notifikace';
 $string['plugin_advanced'] = 'Další nastavení notifikací';
 $string['pluginname'] = 'Vlastní notifikace';
 $string['tasksend'] = 'Vlastní notifikace odeslání emailu';
 $string['taskfill'] = 'Doplnění notifikací ke kurzům';
 $string['modulename'] = 'Vlastní notifikace';
 $string['noPeriodicText'] = 'Zatím nebyl zadán výchozí text.';
 $string['noOneTimeText'] = 'Zatím nebyl zadán výchozí text.';
 $string['categories_enabled'] = 'Zvolte kategorie, kde se budou notifikace pravidelně posílat.';
 $string['one_day'] = 'Povolit notifikaci 1 den před konáním kurzu.';
 $string['three_day'] = 'Povolit notifikaci 3 dny před konáním kurzu.';
 $string['seven_day'] = 'Povolit notifikaci 7 dní před konáním kurzu.';
 $string['one_time'] = 'Povolit jednorázové notifikace.';
 $string['default_text_periodical_title'] = 'Výchozí text pravidelné notifikace.';
 $string['default_text_one_time_title'] = 'Výchozí text jednorázové notifikace.';
 $string['default_text_periodical'] = 'Vážená paní, vážený pane,

 Níže Vám zasíláme informace k on-line kurzu, do kterého jste se zapsal/a.
 Váš kurz [course_url] [days_to_start] .
 Začátek kurzu: [course_startdate]
 Konec kurzu: [course_endtime]
 
 Pozornost prosím věnujte rubrice "Složka souborů k výuce", ve které naleznete studijní materiály a dále také užitečné rady pro snadné připojení a případný postup při odblokování Vašeho zařízení pro vstup do vysílací relace.
 
 Těšíme se na Vaši účast.

 S pozdravem a přáním hezkého dne,
  
 ';

 $string['default_text_one_time'] = 'Vážená paní, vážený pane,
 v kurzu, kterého se účastníte: [course_name],
 došlo ke změne materiálů, prosím navštivte stránky kurzu [course_url], kde se dozvíte více.
    
 S pozdravem a přáním hezkého dne,
   

 ';

 $string['notification_for_course'] = 'Notifikace pro kurz';
 $string['contact_person'] = 'Kontaktní osoba : ';
 $string['contact_persons'] = 'Kontaktní osoby : ';
 $string['no_contact_person'] = 'V tomto kurzu nejsou žádné kontaktní osoby.';
 $string['lector'] = 'Lektor kurzu : ';
 $string['lectors'] = 'Lektoři : ';
 $string['no_lectors'] = 'V tomto kurzu nejsou žádní lektoři.';
 $string['students'] = 'Studenti : ';
 $string['no_students'] = 'V tomto kurzu nejsou žádní studenti.';
 $string['regular_notification'] = 'Pravidelná notifikace';
 $string['hint'] = 'Nápověda';
 $string['text_restriction'] = 'Můžete použít jen a pouze písmena, čísla, standardní znaky a níže definované tagy.';
 $string['tag'] = 'tag';
 $string['meaning'] = 'pozn.';
 $string['course_name'] = 'Celý název kurzu';
 $string['days_to_start'] = 'Počet dní do začátku (....začíná za 1 dny...)';
 $string['course_location'] = 'Vypíše město ve kterém se kurz koná';
 $string['course_location_detail'] = 'Vypíše detailnější popis lokace';
 $string['course_start_date_and_time'] = 'Vypíše datum a čas začátku kurzu';
 $string['course_starttime'] = 'Vypíše čas začátku kurzu';
 $string['course_startdate'] = 'Vypíše datum začátku kurzu';
 $string['course_endtime'] = 'Vypíše pouze čas ukončení kurzu';
 $string['course_end_date_and_time'] = 'Vypíše datum a čas ukončení kurzu';
 $string['course_url'] = 'Vypíše název kurzu, který je zároveň odkazem na kurz';
 $string['notification_not_allowed'] = 'Pro tento kurz nejsou povolené pravidelné ani jednorázové notifikace';
 $string['one_time_heading'] = 'Jednorázová notifikace';
 $string['one_time_subject_text'] = 'Změna v kurzu: [course_name]';
 $string['one_time_subject'] = 'Předmět zprávy: ';
 $string['regular_subject'] = 'Výchozí předmět opakované notifikace ';
 $string['default_regular_subject'] = 'Váš kurz [course_name] [days_to_start]';
 $string['send'] = 'Odeslat';
 $string['submit'] = 'Nastavit';
 $string['no_templates'] = 'Nemáte nastavené žádné vzory pro jednotlivé kategorie. Pro všechny kategorie platí výchozí notifikace. ';
 $string['template_creator'] = 'Zde nastavte zprávu pro kategorii nebo kategorie';
 $string['not_allowed'] = 'Sem nemáte vstup povolen.';
 $string['template_saved'] = 'Váš vzor zprávy byl uložen.';
 $string['category_name'] = 'Kategorie: ';
 $string['delete'] = 'SMAZAT';
 $string['deleted'] = 'Váš vzor byl úspěšně smazán.';
 $string['created_templates'] = 'Počet vytvořených vzorů: ';
 $string['one_day_text'] = 'den';
 $string['days_2-4'] = 'dny';
 $string['days_5_plus'] = 'dní';
 $string['one_day_past'] = 'dnem';
 $string['future'] = 'začíná za ';
 $string['past'] = 'proběhl před ';
 $string['today'] = 'probíhá dnes'; 
 $string['updated'] = 'Vzor vaší pravidelné notifikace pro tento kurz byl upraven.';
 $string['overwrite_all_course_in_category'] = 'Přepsat tímto vzorem už vytvořené vzory?'; 
 $string['resetAllWarninig'] = 'Tímto přepíšete všechny vytvořené zprávy této kategorie.';
 
 $string['enrol_self_subject'] = 'Předmět zápisu sama sebe';
 $string['enrol_self_subject_text'] = 'Notifikace o zápisu do kurzu';
 $string['enrol_self_text_title'] = 'Text notifikace o zapsání sama sebe';
 $string['enrol_self_text_text'] = 'Příklad notifikace';
 $string['unenrol_self_subject'] = 'Předmět při odhlášení sama sebe z kurzu';
 $string['unenrol_self_subject_text'] = 'Předmět';
 $string['unenrol_self_text_title'] = 'Zpráva při odhlášení sama sebe z kurzu';
 $string['unenrol_self_text_text'] = 'Příklad zprávy';
 $string['periodical_setting'] = 'Nastavení periodických notifikací';
 $string['enrol_setting'] = 'Nastavení notifikací zápisů';
 $string['special_enrol_notification_category'] = 'Zvolit kategorii, kde se bude posílat speciální notifikace o zápisu';
 $string['enrol_setting_default'] = 'Výchozí text notifikace ';
 $string['enrol_setting_special'] = 'Notifikace o zápisu pro určené kategorie';

 $string['enrol_manual_subject'] = 'Předmět manuálního zápisu';
 $string['enrol_manual_subject_text'] = 'Notifikace o zápisu do kurzu';
 $string['enrol_manual_text_title'] = 'Text notifikace manuálního zápisu';
 $string['enrol_manual_text_text'] = 'Příklad notifikace';
 $string['unenrol_manual_subject'] = 'Předmět při manuálním odhlášení kurzu';
 $string['unenrol_manual_subject_text'] = 'Předmět';
 $string['unenrol_manual_text_title'] = 'Zpráva při manuálním odhlášení z kurzu';
 $string['unenrol_manual_text_text'] = 'Příklad zprávy';
 $string['tags'] = '[course_url] - odkaz s názvem kurzu 
                    [course_name] - název kurzu 
                    [days_to_start] - počet dní do začátku kurzu 
                    [course_start_date_and_time] - datum a čas začátku kurzu 
                    [course_starttime] - čas začátku kurzu
                    [course_startdate] - datum začítku kurzu
                    [course_endtime] - čas ukončení kurzu
                    [course_enddate] - datum ukončení kurzu
                    [location] - lokace kurzu
                    ';