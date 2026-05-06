<?php

$input = fgets(STDIN);
if (!$input) exit;
list($n, $m) = array_map('intval', explode(' ', trim($input)));

$adj = [];
for ($i = 0; $i < $m; $i++) {
    $line = fgets(STDIN);
    if (!$line) break;
    list($u, $v, $cost) = array_map('intval', explode(' ', trim($line)));
    $adj[$u][] = ['to' => $v, 'cost' => $cost];
}

$dist = array_fill(1, $n, 0);
$parent = array_fill(1, $n, -1);
$count = array_fill(1, $n, 0);
$inQueue = array_fill(1, $n, true);

$queue = new SplQueue();
for ($i = 1; $i <= $n; $i++) {
    $queue->enqueue($i);
}

$cycleNode = -1;

while (!$queue->isEmpty()) {
    $u = $queue->dequeue();
    $inQueue[$u] = false;

    if (!isset($adj[$u])) continue;

    foreach ($adj[$u] as $edge) {
        $v = $edge['to'];
        $weight = $edge['cost'];

        if ($dist[$v] > $dist[$u] + $weight) {
            $dist[$v] = $dist[$u] + $weight;
            $parent[$v] = $u;

            $count[$v]++;
            if ($count[$v] >= $n) {
                $cycleNode = $v;
                break 2;
            }

            if (!$inQueue[$v]) {
                $queue->enqueue($v);
                $inQueue[$v] = true;
            }
        }
    }
}

if ($cycleNode !== -1) {
    echo "YES\n";

    $curr = $cycleNode;
    for ($i = 0; $i < $n; $i++) {
        $curr = $parent[$curr];
    }

    $cycle = [];
    $start = $curr;
    do {
        $cycle[] = $curr;
        $curr = $parent[$curr];
    } while ($curr !== $start);

    $cycle[] = $start;
    $cycle = array_reverse($cycle);

    // Убираем последний элемент (дубликат)
    array_pop($cycle);

    // Нормализация: начинаем с минимальной вершины
    $minVal = min($cycle);
    $minIndex = array_search($minVal, $cycle);

    $res = [];
    $len = count($cycle);
    for ($i = 0; $i < $len; $i++) {
        $res[] = $cycle[($minIndex + $i) % $len];
    }
    $res[] = $minVal;

    echo implode(' ', $res) . "\n";
} else {
    echo "NO\n";
}