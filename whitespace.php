<?php

/**
* Enum Opcodes
*/
abstract class Opcodes {
	const push		= "push";
	const dup		= "dup";
	const swap		= "swap";
	const discard	= "discard";
	const add		= "add";
	const sub		= "sub";
	const mul		= "mul";
	const div		= "div";
	const mod		= "mod";
	const store		= "store";
	const retrieve	= "retrieve";
	const label		= "label";
	const call		= "call";
	const jump		= "jump";
	const jz		= "jz";
	const jn		= "jn";
	const ret		= "ret";
	const quit		= "quit";
	const outchar	= "outchar";
	const outnum	= "outnum";
	const readchar	= "readchar";
	const readnum	= "readnum";
	const signed	= "signed";
	const unsigned	= "unsigned";
}

$opcodes = [
  ['  ',		Opcodes::push, 	Opcodes::signed],
  [' \n ',		Opcodes::dup],
  [' \n\t',		Opcodes::swap],
  [' \n\n',		Opcodes::discard],
  ['\t   ',		Opcodes::add], 
  ['\t  \t',	Opcodes::sub],
  ['\t  \n',	Opcodes::mul],
  ['\t \t ',	Opcodes::div],
  ['\t \t\t',	Opcodes::mod],
  ['\t\t ',		Opcodes::store],
  ['\t\t\t',	Opcodes::retrieve],
  ['\n  ',		Opcodes::label, Opcodes::unsigned],
  ['\n \t',		Opcodes::call, 	Opcodes::unsigned],
  ['\n \n',		Opcodes::jump, 	Opcodes::unsigned],
  ['\n\t ',		Opcodes::jz, 	Opcodes::unsigned],
  ['\n\t\t',	Opcodes::jn, 	Opcodes::unsigned],
  ['\n\t\n',	Opcodes::ret],
  ['\n\n\n',	Opcodes::quit],
  ['\t\n  ',	Opcodes::outchar],
  ['\t\n \t',	Opcodes::outnum],
  ['\t\n\t ',	Opcodes::readchar],
  ['\t\n\t\t',	Opcodes::readnum],
];

/**
* Tokenizer
*/
class Tokenizer {
	
	private $tokens;
	private $program;
	
	function __construct($ws) {
		$this->tokens = [];
		$this->program = $this->cleanup($ws);
		while ($this->program != "") {
			$this->tokens[] = $this->tokenize();
		}
        // foreach ($this->tokens as $token) {
        //     echo pparr($token)."\n";
        // }
	}
	
	public function get_tokens() {
		return $this->tokens;
	}
	
	private function cleanup($ws) {
		return preg_replace("/[^ \t\n]/", "", $ws);
	}
	
	private function tokenize() {
		global $opcodes;
		foreach ($opcodes as $opcode) {
			list($ws, $symbol, $args) = array_pad($opcode, 3, NULL);
			$pattern = "/^".$ws.($args ? "([ \t]*)\n" : "()")."(.*)\$/s";
			if (preg_match($pattern, $this->program, $match)) {
				$this->program = $match[2];
				switch ($args) {
					case Opcodes::unsigned :
						$bin = bindec(str_replace([" ", "\t"], [0, 1], $match[1]));
						return [$symbol, $bin];
					case Opcodes::signed :
						$bin = bindec(str_replace([" ", "\t"], [0, 1], substr($match[1], 1)));
						if (strlen($match[1]) > 0 && $match[1][0] == "\t") $bin *= -1;
						return [$symbol, $bin];
					default:
						return [$symbol];
				}
			}
		}
        // foreach ($this->tokens as $token) {
        //     echo pparr($token)."\n";
        // }
		die("Unknown command: ".str_replace([" ", "\t", "\n"], ["S", "T", "L"], $this->program));
	}
}

class Interpreter {
	
	private $tokens;
	
	function __construct($tokens) {
		$this->tokens = $tokens;
	}
	
	public function run() {
		$this->pc = 0;
		$stack = [];
		$heap = [];
		$callstack = [];
		while (true) {
			if (!isset($this->tokens[$this->pc])) {
				die("Program ended with the programcounter going above the number of instructions.");
			}
			list($opcode, $arg) = array_pad($this->tokens[$this->pc], 2, NULL);
			$this->pc++;
			
			switch ($opcode) {
				case Opcodes::push :
					$stack[] = $arg;
					break;
				case Opcodes::dup :
					$stack[] = end($stack);
					break;
				case Opcodes::swap :
					$tmp = $stack[count($stack)-1];
					$stack[count($stack)-1] = $stack[count($stack)-2];
					$stack[count($stack)-2] = $tmp;
					break;
				case Opcodes::discard :
					array_pop($stack);
					break;
				case Opcodes::add :
				case Opcodes::sub :
				case Opcodes::mul :
				case Opcodes::div :
				case Opcodes::mod :
				    $b = array_pop($stack);
				    $a = array_pop($stack);
					$stack[] = $this->apply_op($opcode, $a, $b);
					break;
				case Opcodes::store :
					$value = array_pop($stack);
					$address = array_pop($stack);
					$heap[$address] = $value;
					break;
				case Opcodes::retrieve :
					$stack[] = $heap[array_pop($stack)];
					break;
				case Opcodes::label :
					break;
				case Opcodes::call :
					$callstack[] = $this->pc;
					$this->jump($arg);
					break;
				case Opcodes::jump :
					$this->jump($arg);
					break;
				case Opcodes::jz :
					if (array_pop($stack) == 0) $this->jump($arg);
					break;
				case Opcodes::jn :
					if (array_pop($stack) < 0) $this->jump($arg);
					break;
				case Opcodes::ret :
					$this->pc = array_pop($callstack);
					break;
				case Opcodes::quit :
					die();
					break;
				case Opcodes::outchar :
					echo chr(array_pop($stack));
					break;
				case Opcodes::outnum :
					echo array_pop($stack);
					break;
				case Opcodes::readchar :
					$c = fgetc(STDIN);
					// if (!$c) exit(0);
					$heap[array_pop($stack)] = ord($c);
					break;
				case Opcodes::readnum :
					$c = fgets(STDIN);
					// if (!$c) exit(0);
					$heap[array_pop($stack)] = (int)$c;
					break;
				default:
					die("Unknown instruction: $opcode");
					break;
			}
			// echo $opcode.":\n";
			// echo "  Stack: ".pparr($stack)."\n";
			// echo "  Heap: ".pparrkey($heap, true)."\n";
		}
	}
	
	private function apply_op($op, $x, $y) {
		switch($op) {
			case Opcodes::add :
				return $x + $y;
			case Opcodes::sub :
				return $x - $y;
			case Opcodes::mul :
				return $x * $y;
			case Opcodes::div :
				return $x / $y;
			case Opcodes::mod :
				return $x % $y;
			 default:
				die('Operand not a valid operator.');
		}
	}
	
	private function jump($label) {
		foreach ($this->tokens as $pc => $token) {
			if ($token == [Opcodes::label, $label]) {
				$this->pc = $pc;
				return;
			}
		}
		die("Label not found: $label");
	}
	
}

if ($_SERVER['argc'] == 2) {
	$file = $_SERVER['argv'][1];
	if (file_exists($file)) {
		$code = file_get_contents($file);
	} else if (file_exists(__DIR__.'/'.$file)) {
		$code = file_get_contents(__DIR__.'/'.$file);
	} else {
		exit("Could not open file: $file\n");
	}

	$tokenizer = new Tokenizer($code);
	$interpreter = new Interpreter($tokenizer->get_tokens());
	$interpreter->run();
} else {
	echo "Usage: php whitespace.php whitespace.ws\n";
}

function pparr($arr) {
	return "[".join(", ", $arr)."]";
}

function pparrkey($arr) {
	$r = "["; $i = 0;
	foreach ($arr as $key => $value) {
		if ($i != 0) $r .= ", ";
		$r .= "$key => $value";
		$i++;
	}
	return $r."]";
}