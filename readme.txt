=== Number to Bangla ===
Contributors: rkb007
Tags: number, bangla, bengali, money, date
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert English numbers to Bangla digits, words, money, dates, ordinals and more — via shortcode, block, REST API or PHP helpers.

== Description ==
Number to Bangla converts English numbers into Bangla in many formats. Use it with the `[ntb_num]` shortcode, the "Number to Bangla" Gutenberg block, the REST API, or directly from your theme with template helper functions.

Supported formats:

- **number** — English digits to Bangla digits (`111` → `১১১`)
- **word** — Bangla words, with decimals and negatives (`1345.05` → `এক হাজার তিন শত পঁয়তাল্লিশ দশমিক শূন্য পাঁচ`)
- **money** — Taka/Poisha words (`1345.50` → `এক হাজার তিন শত পঁয়তাল্লিশ টাকা পঞ্চাশ পয়সা`)
- **comma** — lakh-grouped commas (`121212121` → `১২,১২,১২,১২১`)
- **percentage** — `75` → `৭৫ শতাংশ` (add `words="true"` for spelled-out)
- **month** — Gregorian month name (`12` → `ডিসেম্বর`)
- **bengali-month** — Bengali calendar month (`1` → `বৈশাখ`)
- **season** — Bengali season (`1` → `গ্রীষ্ম`)
- **day** — weekday from number or English name (`monday` → `সোমবার`)
- **date** — formatted Bangla date (`2024-01-15` → `১৫ জানুয়ারি, ২০২৪`)
- **time** — clock or spoken time (`14:30` → `১৪:৩০`, or words → `দুপুর দুইটা ত্রিশ মিনিট`)
- **duration** — seconds to Bangla (`3665` → `১ ঘণ্টা ১ মিনিট ৫ সেকেন্ড`)
- **age** — age from a birth date (`1990-01-15` → `৩৫ বছর …`)
- **ordinal** — Bangla ordinals (`1` → `১ম`, `25` → `২৫তম`)
- **parse** — reverse: Bangla digits to English (`১২৩৪৫` → `12345`)

== Installation ==
INSTALLATION FROM WITHIN WORDPRESS
- Visit Plugins > Add New.
- Search for "Number to Bangla".
- Install and activate "Number to Bangla".

MANUAL INSTALLATION
- Upload the entire "number-to-bangla" folder to the /wp-content/plugins/ directory.
- Visit Plugins.
- Activate "Number to Bangla" plugin.

== Usage ==
Shortcode:

`[ntb_num value="111" format="word"]` → `এক শত এগারো`

Optional attributes: `prefix`, `suffix`, `words` (for percentage/time), `date_format` (for date), `as_of` (for age).

`[ntb_num value="2024-01-15" format="date" date_format="d/m/Y"]` → `১৫/০১/২০২৪`
`[ntb_num value="123" format="ordinal" prefix="অবস্থান: "]` → `অবস্থান: ১২৩তম`

Block:
Add the "Number to Bangla" block in the editor and choose a value and format. Output is rendered live.

REST API:
`GET /wp-json/ntb/v1/convert?value=1345.50&format=money`

Template helpers (PHP):
`ntb_to_word( 111 )`, `ntb_to_number( 111 )`, `ntb_to_money( 1345.50 )`, `ntb_to_date( '2024-01-15' )`, `ntb_ordinal( 25 )`, and the generic `ntb_convert( $value, $format, $args )`.

Developers can filter output via the `ntb_convert_output` filter.

== Screenshots ==
1. Using the `[ntb_num]` shortcode to convert numbers in a post.
2. Example output of the available Bangla formats.

== Changelog ==

= 2.0.0 =
* New formats: percentage, bengali-month, season, day, date, time, duration, age, ordinal, and reverse parse.
* New "Number to Bangla" Gutenberg block with live preview.
* New REST API endpoint `ntb/v1/convert`.
* New template helper functions and the `ntb_convert_output` filter.
* New shortcode attributes: prefix, suffix, words, date_format, as_of.
* Refactored into an `includes/` structure; added ABSPATH guards, output escaping and attribute sanitization.
* Added i18n (text domain loading + translation template).
* Fixed trailing spaces in word output and added negative-number support (ঋণাত্মক).
* Added PHPUnit test suite.
* Bumped: Requires PHP 7.4, Tested up to 6.5.

= 1.1.0 =
* Refactored conversion methods; decimal and money handling improvements.

= 1.0.0 =
* Initial release: number, word, money, month and comma formats via the `[ntb_num]` shortcode.
