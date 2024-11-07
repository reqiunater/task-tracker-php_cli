#!/usr/bin/env php
<?php
// var_dump($argv);

// Get the json file
$json_file = file_get_contents('task-data.json');

if ($json_file === false) {
    die('Error reading the JSON file');
}

// check if json is null
$json_data = json_decode($json_file, true);
if ($json_data === null) {
    die('Error decoding the JSON file');
}

// get the 1st argument ex. list || add etc
$action = $argv[1];

// get the 2nd argument ex. id and description
$subAct = $argv[2];

// get the third arguments for update
$subSubAct = (!empty($argv[3])) ? $argv[3] : '' ;

// check the action
switch ($action) {

    // for action list
    case 'list':

        //this is for beautify the output
        printf("%-4s | %-20s | %-10s | %-19s | %-19s\n", "Id", "Description", "Status", "Create at", "Updated At");
        echo str_repeat("-", 80) . "\n";

        foreach ($json_data as $data) {
            foreach ($data as $row) {

                // check if subAct is empty
                if (!empty($subAct)) {

                    // if empty, display data where status == subAct
                    if ($subAct == $row['status']) {
                        printf(
                            "%-4s | %-20s | %-10s | %-19s | %-19s\n",
                            $row['id'],
                            $row['description'],
                            $row['status'],
                            $row['createdAt'],
                            $row['updatedAt']
                        );
                    }
                } else {
                    // if subAct is null or empty, display all
                    printf(
                        "%-4s | %-20s | %-10s | %-19s | %-19s\n",
                        $row['id'],
                        $row['description'],
                        $row['status'],
                        $row['createdAt'],
                        $row['updatedAt']
                    );
                }
            }
        }
        break;
    case 'add':
        if (empty($subAct)) {
            die("Please write task you want to add\n");
        }

        // get the highest id
        $listId = array_column($json_data['task'], 'id');
        $currentId = empty($listId) ? 1 : (max($listId) + 1);

        $newTask = [
            'id' => $currentId,
            'description' => $subAct,
            'status' => "todo",
            'createdAt' => date("Y-m-d H:i:s"),
            'updatedAt' => date("Y-m-d H:i:s") 
        ];

        $json_data['task'][] = $newTask;

        $updatedJson = json_encode($json_data, JSON_PRETTY_PRINT);
        $add = putToJson($json_data);
        if ($add) {
            echo "Success add new task\n";
        } else {
            echo "Error insert task\n";
        }
        
        break;

    case 'delete':
        $found = false;
        foreach ($json_data['task'] as $key => $item) {
            if ($subAct == $item['id']) {
                unset($json_data['task'][$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            $json_data['task'] = array_values($json_data['task']);
            $delete = putToJson($json_data);
            if ($delete) {
                echo "Task deleted successfully\n";
            } else {
                echo "Error delete Tas\n";
            }
        } else {
            echo "Task with id $subAct not found\n";
        }
        break;
    case 'update':
        $found = false;
        foreach ($json_data['task'] as $key => $item) {
            if ($subAct == $item['id']) {
                $json_data['task'][$key]['description'] = $subSubAct;
                $found = true;
            }
        }

        if ($found) {
            $json_data['task'] = array_values($json_data['task']);
            $update = putToJson($json_data);
            if ($update) {
                echo "Task updates successfully\n";
            } else {
                echo "Error update Tas\n";
            }
        } else {
            echo "id $subAct not found!";
        }
        break;
    case 'mark-in-progress':
        $found = false;
        foreach ($json_data['task'] as $key => $item) {
            if ($subAct == $item['id']) {
                $json_data['task'][$key]['status'] = 'in_progress';
                $found = true;
            }
        }

        if ($found) {
            $json_data['task'] = array_values($json_data['task']);
            $update = putToJson($json_data);
            if ($update) {
                echo "Task updates successfully\n";
            } else {
                echo "Error update Tas\n";
            }
        } else {
            echo "id $subAct not found!";
        }
        break;
    case 'mark-done':
        $found = false;
        foreach ($json_data['task'] as $key => $item) {
            if ($subAct == $item['id']) {
                $json_data['task'][$key]['status'] = 'done';
                $found = true;
            }
        }

        if ($found) {
            $json_data['task'] = array_values($json_data['task']);
            $update = putToJson($json_data);
            if ($update) {
                echo "Task updates successfully\n";
            } else {
                echo "Error update Tas\n";
            }
        } else {
            echo "id $subAct not found!";
        }
        break;

    case 'help':
        echo "list - To see all task\n";
        echo "list <done | todo> - To see all task with status done or todo\n";
        echo "add - to add new task\n";
        // echo "update - to update";
        break;
    default:
        echo "Unknown Command!! \nsee help\n";
        break;
}

function putToJson($array) {
    $dataSend = json_encode($array, JSON_PRETTY_PRINT);
    return (file_put_contents("task-data.json", $dataSend)) ? true : false;
}
?>