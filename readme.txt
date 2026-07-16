=== Number to Bangla ===
Contributors: rkb007
Tags: number, bangla, bengali, money, date
Requires at least: 5.6
Tested up to: 7.0.1
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert English numbers to Bangla digits, words, money, dates, ordinals and more — via shortcode, block, REST API, WP-CLI or PHP helpers.

== Description ==
Number to Bangla converts English numbers into Bangla in many formats. Use it with the `[ntb_num]` shortcode, the "Number to Bangla" and "Bangla Clock" Gutenberg blocks, the REST API (including batch conversion), an Elementor dynamic tag, the `wp ntb convert` WP-CLI command, or directly from your theme with template helper functions.

Supported formats:

- **number** — English digits to Bangla digits (`111` → `১১১`)
- **word** — Bangla words, with decimals and negatives (`1345.05` → `এক হাজার তিন শত পঁয়তাল্লিশ দশমিক শূন্য পাঁচ`)
- **money** — Taka/Poisha words (`1345.50` → `এক হাজার তিন শত পঁয়তাল্লিশ টাকা পঞ্চাশ পয়সা`)
- **comma** — lakh-grouped commas (`121212121` → `১২,১২,১২,১২১`)
- **percentage** — `75` → `৭৫ শতাংশ` (add `words="true"` for spelled-out)
- **month** — Gregorian month name (`12` → `ডিসেম্বর`)
- **bengali-month** — Bengali calendar month (`1` → `বৈশাখ`)
- **bengali-date** — full reformed Bangla calendar date (`2024-04-14` → `১ বৈশাখ, ১৪৩১`)
- **season** — Bengali season (`1` → `গ্রীষ্ম`)
- **day** — weekday from number or English name (`monday` → `সোমবার`)
- **date** — formatted Bangla date (`2024-01-15` → `১৫ জানুয়ারি, ২০২৪`)
- **week** — ISO week number in Bangla digits (`2024-01-01` → `১`)
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

Blocks:
Add the "Number to Bangla" block in the editor and choose a value and format. Output is rendered live. The "Bangla Clock" block shows a live-ticking clock in Bangla digits (optionally with seconds).

REST API:
`GET /wp-json/ntb/v1/convert?value=1345.50&format=money`

Convert several values in one request with the batch endpoint (max 100 items):

`POST /wp-json/ntb/v1/batch`
`{"items":[{"value":"111","format":"word"},{"value":"2024-01-15","format":"bengali-date"}]}`

WP-CLI:
`wp ntb convert 111 --format=word`
`wp ntb convert 2024-01-15 --format=bengali-date`

Elementor:
When Elementor is active, a "Number to Bangla" dynamic tag is available under the Text category for any text field.

Template helpers (PHP):
`ntb_to_word( 111 )`, `ntb_to_number( 111 )`, `ntb_to_money( 1345.50 )`, `ntb_to_date( '2024-01-15' )`, `ntb_to_bengali_date( '2024-01-15' )`, `ntb_week_number( '2024-01-15' )`, `ntb_ordinal( 25 )`, and the generic `ntb_convert( $value, $format, $args )`.

Developers can filter output via the `ntb_convert_output` filter.

== Screenshots ==
1. All conversion formats rendered on the front-end with the `[ntb_num]` shortcode.
2. The "Number to Bangla" block in the editor — value, format, prefix and suffix with a live preview.
3. Inserting the "Number to Bangla" block from the block inserter.

== Changelog ==

= 2.1.0 =
* Compatibility: tested up to WordPress 7.0.1.
* New formats: `bengali-date` (full reformed Bangla calendar date, day/month/year) and `week` (ISO week number).
* New "Bangla Clock" block: a live-ticking clock in Bangla digits, updating client-side every second.
* New REST endpoint `POST ntb/v1/batch` to convert up to 100 values in a single request.
* New WP-CLI command: `wp ntb convert <value> --format=<format>`.
* New Elementor dynamic tag ("Number to Bangla") available when Elementor is active.
* New template helpers: `ntb_to_bengali_date()`, `ntb_week_number()`.

= 2.0.1 =
* Compatibility: tested up to WordPress 7.0; raised "Requires at least" to 5.6 (matches the block editor APIs used).
* Fixed timezone-sensitive date/time conversion by parsing dates with DateTime in UTC instead of the runtime-dependent date() function.
* Removed the manual text-domain loading call; WordPress loads translations automatically for the matching plugin slug (4.6+).
* Lowered the maximum convertible number to 999,999,999 (was 999,999,999,999,999): values at or above 1 arab produced non-idiomatic Bangla word output ("X কোটি Y কোটি ...").

= 2.0.0 =
* New formats: percentage, bengali-month, season, day, date, time, duration, age, ordinal, and reverse parse.
* New "Number to Bangla" Gutenberg block with live preview.
* New REST API endpoint `ntb/v1/convert`.
* New template helper functions and the `ntb_convert_output` filter.
* New shortcode attributes: prefix, suffix, words, date_format, as_of.
* Refactored into an `includes/` structure; added ABSPATH guards, output escaping and attribute sanitization.
* Added i18n (text domain loading + translation template).
* Fixed trailing spaces in word output and added negative-number support (ঋণাত্মক).
* Fixed Gutenberg block registration in the editor (declared script dependencies) and preserved spaces in prefix/suffix.
* Added PHPUnit test suite.
* Bumped: Requires PHP 7.4, Tested up to 6.5.

= 1.1.0 =
* Refactored conversion methods; decimal and money handling improvements.

= 1.0.0 =
* Initial release: number, word, money, month and comma formats via the `[ntb_num]` shortcode.
