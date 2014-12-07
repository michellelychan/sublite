<?php
  function vprocess() {
    global $viewVars;
    if (isset($_SESSION['loggedin'])) {
      $viewVars = array_merge($viewVars, array(
        'Loggedin' => true,
        'L_id' => $_SESSION['_id'],
        'Lemail' => $_SESSION['email'],
        'Lpass' => $_SESSION['pass']
      ));
    } else {
      $viewVars['Loggedin'] = false;
    }
  }
  function vecho($var, $format = null) {
    global $viewVars;
    vprocess();
    if (isset($viewVars[$var])) {
      $var = $viewVars[$var];
      if ($format == null) $format = "{var}";
      $format = str_replace("{var}", $var, $format);
      echo $format;
    }
  }
  function vget($var) {
    global $viewVars;
    vprocess();
    return $viewVars[$var];
  }
  function vnotice() {
    vecho('Success', "<div class=\"success\">{var}</div>");
    vecho('Error', "<div class=\"error\">{var}</div>");
  }
?>