<?php
//get tasklist array from POST
$task_list_string = filter_input(INPUT_COOKIE, 'tasklist');

if ($task_list_string === NULL) {
    $task_list = array();
    $task_list[] = 'Write chapter';
    $task_list[] = 'Edit chapter';
    $task_list[] = 'Proofread chapter';
}
else{
	$task_list_string = stripslashes($task_list_string);
	$task_list = json_decode($task_list_string);
}

//get action variable from POST
$action = filter_input(INPUT_POST, 'action');

//initialize error messages array
$errors = array();

//process
switch( $action ) {
    case 'Add Task':
        $new_task = filter_input(INPUT_POST, 'newtask');
        if (empty($new_task)) {
            $errors[] = 'The new task cannot be empty.';
        } else {
            // $task_list[] = $new_task;
            array_push($task_list, $new_task);
        }
        break;
    case 'Delete Task':
        $task_index = filter_input(INPUT_POST, 'taskid', FILTER_VALIDATE_INT);
        if ($task_index === NULL || $task_index === FALSE) {
            $errors[] = 'The task cannot be deleted.';
        } else {
            unset($task_list[$task_index]);
            $task_list = array_values($task_list);
        }
        break;
    case 'Modify Task':
        $task_index = filter_input(INPUT_POST, 'taskid', FILTER_VALIDATE_INT);
        if ($task_index === NULL || $task_index === FALSE) {
            $errors[] = 'The task cannot be modified.';
        } else {
            $task_to_modify = $task_list[$task_index];
        }
        break;
    case 'Save Changes':
        $i = filter_input(INPUT_POST, 'modifiedtaskid', FILTER_VALIDATE_INT);
        $modified_task = filter_input(INPUT_POST, 'modifiedtask');
        if (empty($modified_task)) {
            $errors[] = 'The modified task cannot be empty.';
        } elseif($i === NULL || $i === FALSE) {
            $errors[] = 'The task cannot be modified.';        
        } else {
            $task_list[$i] = $modified_task;
            $modified_task = '';
        }
        break;
    case 'Cancel Changes':
        $modified_task = '';
        break;
    case 'Promote Task':
        $task_index = filter_input(INPUT_POST, 'taskid', FILTER_VALIDATE_INT);
        if ($task_index === NULL || $task_index === FALSE) {
            $errors[] = 'The task cannot be promoted.';
        } elseif ($task_index == 0) {
            $errors[] = 'You can\'t promote the first task.';
        } else {
            // get the values for the two indexes
            $task_value = $task_list[$task_index];
            $prior_task_value = $task_list[$task_index-1];

            // swap the values
            $task_list[$task_index-1] = $task_value;
            $task_list[$task_index] = $prior_task_value;
            break;
        }
    case 'Sort Tasks':
        sort($task_list);
        break;
}

$task_list_string = json_encode($task_list);
setcookie('tasklist', $task_list_string, strtotime('+1 month'));
include('task_list.php');
?>