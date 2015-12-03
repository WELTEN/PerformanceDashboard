If elgg file views/default/page_elements/header.php sets document mode (X-UA-Compatible) to IE7 or IE8,
copy header.php in this folder and strip the file from its IE version dependency, so IE uses its own latest Javascript engine.
The d3.js library (see vendors folder) does not support under IE7 or 8.