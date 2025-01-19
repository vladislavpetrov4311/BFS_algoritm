<?php

function read_input() {
    $num_lines = [];
    while (true) {
        $line = trim(fgets(STDIN)); 
        if ($line === "") { 
            break;
        }
        $num_lines[] = $line; 
    }
    return $num_lines; 
}

function parse_input($input_lines) {
    list($rows, $cols) = explode(" ", $input_lines[0]);
    $maze = [];
    for ($i = 1; $i <= $rows; $i++) {
        $maze[] = array_map('intval', explode(" ", $input_lines[$i]));
    }
    list($start_row, $start_col, $finish_row, $finish_col) = array_map('intval', explode(" ", $input_lines[$rows + 1]));
    return [$rows, $cols, $maze, [$start_row, $start_col], [$finish_row, $finish_col]]; 
}

function is_valid_move($maze, $visited, $row, $col) {
    return (
        $row >= 0 &&
        $row < count($maze) &&
        $col >= 0 &&
        $col < count($maze[0]) &&
        $maze[$row][$col] >= 0 && $maze[$row][$col] <= 9 && // позволяет любые значения от 0 до 9
        !$visited[$row][$col]
    );
}

function bfs($maze, $start, $finish) {
    $rows = count($maze);
    $cols = count($maze[0]);
    $visited = array_fill(0, $rows, array_fill(0, $cols, false));
    $queue = new SplQueue();
    $queue->enqueue([$start[0], $start[1], 0, [[$start[0], $start[1]]]]); // [row, col, distance, path]
    $visited[$start[0]][$start[1]] = true;

    $directions = [[1, 0], [-1, 0], [0, 1], [0, -1]]; // down, up, right, left

    while (!$queue->isEmpty()) {
        [$current_row, $current_col, $distance, $path] = $queue->dequeue();

        if ($current_row === $finish[0] && $current_col === $finish[1]) {
            return [$distance, $path];
        }

        foreach ($directions as [$dr, $dc]) {
            $new_row = $current_row + $dr;
            $new_col = $current_col + $dc;

            if (is_valid_move($maze, $visited, $new_row, $new_col)) {
                $visited[$new_row][$new_col] = true;
                $new_path = $path;
                $new_path[] = [$new_row, $new_col];
                $queue->enqueue([$new_row, $new_col, $distance + 1, $new_path]);
            }
        }
    }

    return [-1, []]; // Если путь не найден
}

function main() {
    $input_lines = read_input();
    list($rows, $cols, $maze, $start, $finish) = parse_input($input_lines);
    list($result, $path) = bfs($maze, $start, $finish);

    if ($result >= 0) {
        echo "Длина пути: $result\n";
        foreach ($path as $coords) {
            echo implode(' ', $coords) . "\n";
        }
        echo ".\n";
    } else {
        echo "Путь не найден.\n";
    }
}

main();
?>