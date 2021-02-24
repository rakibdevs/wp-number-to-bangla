# Wordpress Number to Bangla Plugin
✅ Requires at least: 5.0 
✅ Tested up to: 5.6.2 
✅ Requires PHP: 7.2 
✅ License: GPLv2 or later 


<img src="https://github.com/RakibDevs/wp-number-to-bangla/blob/master/assets/banner-772x250.png">

Convert English numbers to Bangla number or Bangla text, Bangla month name and Bangla Money Format. 
Maximum possible number to covert in Bangla word is 999999999999999

## Description

Easy shortcode plugin for converting any english number to Bangla word or money format or in month name. 

Just use, 
```[ntb_num value=value_here format=format_here][/ntb_num]```


## INSTALLATION FROM WITHIN WORDPRESS

- Visit Plugins > Add New.
- Search for "Number to Bangla".
- Install and activate "Number to Bangla".

## MANUAL INSTALLATION

- Upload the entire "number-to-bangla" folder to the /wp-content/plugins/ directory.
- Visit Plugins.
- Activate "Number to Bangla" plugin.

## USAGE

To convert any number into bangla word, 

`[ntb_num value="111" format="word"][/ntb_num]` 111 is an example number. *Output* `একশত এগারো`


Other available formats:
- word: 

    13459 ->	তেরো হাজার চার শত ঊনষাট
    
    1345.05 ->	এক হাজার তিন শত পঁয়তাল্লিশ দশমিক শূন্য পাঁচ
- number:
    1345.5 ->	১৩৪৫.৫
- money:	
    1345.50 ->	এক হাজার তিন শত পঁয়তাল্লিশ টাকা পঞ্চাশ পয়সা
- month:	
    12 ->	ডিসেম্বর
- comma:	
    121212121 ->	১২,১২,১২,১২১
