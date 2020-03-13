<?php

/**
 * Pages Controller
 *
 * @package Cornerstone
 */

class Page extends Controller
{

  // Index Page
  public function index(...$params)
  {

    $this->load->view('pages/index', $params);
  }

  // Get changelog contents
  public function changelog()
  {

    if (file_exists(DIR_ROOT . 'CHANGELOG.md')) {
      $data['contents'] = str_replace('`', '\`', addslashes(file_get_contents(DIR_ROOT . 'CHANGELOG.md')));

      $this->load->view('pages/changelog', $data);
    } else { // File doesn't exist. Redirect to error page.

      $this->error();
    }
  }

  // Testing page
  public function cstest(...$params)
  {

    // $notification = new Notification([
    //   'recipient' => '1',
    //   'type' => 'changelog.new',
    //   'content' => array('text' => 'The site has been updated to version 1')
    // ]);
    // $notificationManager = new NotificationManager();
    // // $notificationManager->add($notification);
    // $notifs = $notificationManager->get(1);

    // if ($notifs) {
    //   echo '<pre>';
    //   print_r($notifs);
    //   echo '</pre>';
    // } else {
    //   echo 'There are no unread notifications';
    // }
    // exit;

    // $this->request->set_params($params);

    // if (isset($this->request->params['search'])) {
    //   echo $this->request->params['search'];
    // }

    // echo '<pre>';
    // print_r($this->request);
    // echo '</pre>';
    // exit;

    $this->load->view('pages/cs-test');
  }
}
