Whitespace Interpreter in PHP
======
Whitespace is an esoteric programming language. Unlike most programming languages, which ignore or assign little meaning to most whitespace characters, the Whitespace interpreter ignores any non-whitespace characters. Only spaces, tabs and linefeeds have meaning.
This is an interpreter for Whitespace, written in PHP.

## Usage
```shell
$ git clone https://github.com/josso/whitespace-interpreter-php.git
$ cd whitespace-interpreter-php
$ php whitespace.php [filename.ws]
```

## Examples

```shell
$ php whitespace.php examples/helloworld.ws
Hello, World!
$ php whitespace.php examples/name.ws
Please enter your name: World!
Hello World!

$ echo World! | php whitespace.php examples/name.ws
Please enter your name: Hello World!
```

## License 
The Brainfuck Interpreter is licensed under The MIT License.

> The MIT License (MIT)
> 
> Copyright (c) 2014 Johan K. Jensen
> 
> Permission is hereby granted, free of charge, to any person obtaining a copy
> of this software and associated documentation files (the "Software"), to deal
> in the Software without restriction, including without limitation the rights
> to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
> copies of the Software, and to permit persons to whom the Software is
> furnished to do so, subject to the following conditions:
> 
> The above copyright notice and this permission notice shall be included in all
> copies or substantial portions of the Software.
> 
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
> SOFTWARE.

## Contact
* Website: [johanjensen.dk](http://johanjensen.dk)
* Twitter: [@johankjensen](https://twitter.com/johankjensen "johankjensen on Twitter")