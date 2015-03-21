<?php
/*
  *
  * URL Patterns
  *     Each pattern is a regex pattern to direct into each module...
  *     there is no module specific patterns here those will be handled
  *     by the individual models.
  *
  */

$URLS = array(
    //array('module','regex'),
    array('admin','(admin)/([\w]+)/([\w \d]+)/([\w \d]+)'),
    array('admin','(admin)/([\w]+)/([\w \d]+)'),
    array('admin','(admin)/([\w]+)'),
    array('admin','(admin)'),
    array('home', '([\w]+)'),
    array('home','')
)
?>