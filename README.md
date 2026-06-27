# WordPress Number to Bangla Plugin

✅ Requires at least: 5.0
✅ Tested up to: 6.5
✅ Requires PHP: 7.4
✅ Stable tag: 2.0.0
✅ License: GPLv2 or later

<img src="https://github.com/RakibDevs/wp-number-to-bangla/blob/master/assets/banner-772x250.png">

Convert English numbers to Bangla digits, words, money, dates, months, seasons, durations, ages, ordinals and more — via shortcode, Gutenberg block, REST API or PHP template helpers.

Maximum convertible number is **999,999,999,999,999**.

## Description

Use the `[ntb_num]` shortcode, the **Number to Bangla** block, the REST API, or template helper functions to convert numbers into Bangla in many formats.

```
[ntb_num value="value_here" format="format_here"]
```

## Shortcode formats

| Format | Example | Output |
| --- | --- | --- |
| `number` | `[ntb_num value="111" format="number"]` | ১১১ |
| `word` | `[ntb_num value="1345.05" format="word"]` | এক হাজার তিন শত পঁয়তাল্লিশ দশমিক শূন্য পাঁচ |
| `money` | `[ntb_num value="1345.50" format="money"]` | এক হাজার তিন শত পঁয়তাল্লিশ টাকা পঞ্চাশ পয়সা |
| `comma` | `[ntb_num value="121212121" format="comma"]` | ১২,১২,১২,১২১ |
| `percentage` | `[ntb_num value="75" format="percentage"]` | ৭৫ শতাংশ |
| `month` | `[ntb_num value="12" format="month"]` | ডিসেম্বর |
| `bengali-month` | `[ntb_num value="1" format="bengali-month"]` | বৈশাখ |
| `season` | `[ntb_num value="1" format="season"]` | গ্রীষ্ম |
| `day` | `[ntb_num value="monday" format="day"]` | সোমবার |
| `date` | `[ntb_num value="2024-01-15" format="date"]` | ১৫ জানুয়ারি, ২০২৪ |
| `time` | `[ntb_num value="14:30" format="time"]` | ১৪:৩০ |
| `duration` | `[ntb_num value="3665" format="duration"]` | ১ ঘণ্টা ১ মিনিট ৫ সেকেন্ড |
| `age` | `[ntb_num value="1990-01-15" format="age"]` | ৩৫ বছর … |
| `ordinal` | `[ntb_num value="25" format="ordinal"]` | ২৫তম |
| `parse` | `[ntb_num value="১২৩৪৫" format="parse"]` | 12345 |

### Optional shortcode attributes

- `prefix`, `suffix` — text wrapped around the output.
- `words="true"` — spell out `percentage` and `time` values in words.
- `date_format="d/m/Y"` — output format for `date` (PHP `date()` tokens: `d j m n F Y y l D`).
- `as_of="2025-03-20"` — reference date for `age`.

```
[ntb_num value="14:30" format="time" words="true"]   →  দুপুর দুইটা ত্রিশ মিনিট
[ntb_num value="123" format="ordinal" prefix="অবস্থান: "]  →  অবস্থান: ১২৩তম
```

## Gutenberg block

Add the **Number to Bangla** block in the editor, pick a value and format, and the output renders live (powered by the REST endpoint). The block is server-rendered, so the front-end always reflects current conversion logic.

## REST API

```
GET /wp-json/ntb/v1/convert?value=1345.50&format=money
→ { "input": "1345.50", "format": "money", "output": "এক হাজার তিন শত পঁয়তাল্লিশ টাকা পঞ্চাশ পয়সা" }
```

Query args: `value` (required), `format` (required), `prefix`, `suffix`, `words`, `date_format`.

## Template helpers (PHP)

```php
ntb_to_number( 111 );          // ১১১
ntb_to_word( 111 );            // এক শত এগারো
ntb_to_money( 1345.50 );       // এক হাজার তিন শত পঁয়তাল্লিশ টাকা পঞ্চাশ পয়সা
ntb_to_date( '2024-01-15' );   // ১৫ জানুয়ারি, ২০২৪
ntb_ordinal( 25 );             // ২৫তম
ntb_convert( $value, $format, $args ); // generic dispatcher
```

Filter the output with the `ntb_convert_output` filter.

## Installation

### From within WordPress
- Visit Plugins > Add New.
- Search for "Number to Bangla".
- Install and activate "Number to Bangla".

### Manual installation
- Upload the entire `number-to-bangla` folder to `/wp-content/plugins/`.
- Visit Plugins and activate "Number to Bangla".

## Development

Run the unit tests with PHPUnit (no WordPress required — the converter is framework-free):

```
phpunit -c phpunit.xml.dist
```
