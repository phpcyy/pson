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
 
![](https://ws1.sinaimg.cn/large/c35c3fddgy1g0s6ngw49cj20mb02b0sn.jpg)

You can add pson into your environment variable ($PATH), and you can use it in anywhere.

Just enjoy it.
