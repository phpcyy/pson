# pson

Json pretty show with colors in terminal.


## Usage

Simple usage: 

```bash
composer g require phpcyy/pson

# Format json from file.
~/.composer/vendor/bin/pson file.json

# Or from STDIN (standard input).
echo '{"message": "hello, 世界"}' | ~/.composer/vendor/bin/pson
```

And you will see:
 
![](https://oss.gonever.com/php6Nq6TD5e7ab5793b694.png)

You may add pson into your environment variable ($PATH), and you can use it anywhere.

Just enjoy it.
