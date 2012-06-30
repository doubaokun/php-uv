--TEST--
Check for uv_spawn
--FILE--
<?php
$in  = uv_pipe_init(uv_default_loop(), 1);
$out = uv_pipe_init(uv_default_loop(), 1);

echo "HELLO ";

$process = uv_spawn(uv_default_loop(), "php", array('-r','echo $_ENV["HELLO"];'), array(
    "cwd" => "/usr/bin/",
    "pipes" => array(
	$in,
        $out,
    ),
    "env" => array(
        "KEY" => "VALUE",
        "HELLO" => "WORLD",
    )
),function($process, $stat, $signal) use ($out){
    uv_close($process,function(){
    });
});

uv_read_start($out, function($buffer,$stat) use ($out){
    echo $buffer . PHP_EOL;

    uv_close($out,function(){});
});

uv_run();
--EXPECT--
HELLO WORLD