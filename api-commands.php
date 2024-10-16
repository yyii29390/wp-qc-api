<?php
global $api_commands;
$api_commands = array(
    'addsub' => array(
        'imsi' => array('required' => true, 'type' => 'string', 'max_length' => 15),
        'iccid' => array('required' => false, 'type' => 'string', 'max_length' => 20),
        'msisdn' => array('required' => true, 'type' => 'string', 'max_length' => 15),
        'planCode' => array('required' => true, 'type' => 'string', 'max_length' => 8),
        'validity' => array('required' => true, 'type' => 'int', 'max_length' => 5),
        'lastActiveTime' => array('required' => false, 'type' => 'string', 'max_length' => 6),
        'initBalance' => array('required' => false, 'type' => 'int', 'max_length' => 10)
    ),
    'changeMSISDN' => array(
        'imsi' => array('required' => true, 'type' => 'string', 'max_length' => 15),
        'newMsisdn' => array('required' => true, 'type' => 'string', 'max_length' => 15)
    ),
    'delsub' => array(
        'imsi' => array('required' => true, 'type' => 'string', 'max_length' => 15)
    ),
    'suspend' => array(
        'imsi' => array('required' => true, 'type' => 'string', 'max_length' => 15),
        'serviceId' => array('required' => false, 'type' => 'string', 'max_length' => 2)
    ),
    'recover' => array(
        'imsi' => array('required' => true, 'type' => 'string', 'max_length' => 15),
        'serviceId' => array('required' => false, 'type' => 'string', 'max_length' => 2)
    ),
    'qrysub' => array(
        'imsi' => array('required' => true, 'type' => 'string', 'max_length' => 15),
        'iccid' => array('required' => false, 'type' => 'string', 'max_length' => 2),
		'msisdn' => array('required' => true, 'type' => 'string', 'max_length' => 15)
    ),
    'addpack' => array(
        'imsi' => array('required' => true, 'type' => 'string', 'max_length' => 15),
        'packCode' => array('required' => true, 'type' => 'string', 'max_length' => 8),
		'activeType' => array('required' => false, 'type' => 'string', 'max_length' => 1),
        'activeDate' => array('required' => false, 'type' => 'string', 'max_length' => 8),
        'validity' => array('required' => false, 'type' => 'int', 'max_length' => 5)		
    ),

    // Add more commands as needed
);
