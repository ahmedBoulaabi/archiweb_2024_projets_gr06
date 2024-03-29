<?php
// Define an associative array with background colors based on the goal
$backgroundColors = [
  'gain-weight-normal' => '#c8f7dc',
  'lose-weight-fast' => '#ffd3e2',
  'lose-weight-normal' => '#e9e7fd'
];

$spanColors = [
  'gain-weight-normal' => '#34c471',
  'lose-weight-fast' => '#df3670',
  'lose-weight-normal' => '#4f3ff0'
];
foreach ($data as $row) :


  // Get the background color based on the goal
  $goal = htmlspecialchars($row->goal); // Assuming $row->goal contains the goal information
  $backgroundColor = isset($backgroundColors[$goal]) ? $backgroundColors[$goal] : '';
  $id = $row->id ?? -1;
  $spanColor = isset($spanColors[$goal]) ? $spanColors[$goal] : '';

  // If no specific color is found -> white used
  if (!$backgroundColor) {
    $backgroundColor = '#ffffff';
  }
?>


  <div class="project-box-wrapper" data-id="<?= $id ?>">
    <div class="project-box" style="background-color: <?= $backgroundColor ?>;">
      <div class="project-box-header">
        <span>December 10, 2020</span>
        <div class="more-wrapper">
          <button class="project-btn-more">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical">
              <circle cx="12" cy="12" r="1" />
              <circle cx="12" cy="5" r="1" />
              <circle cx="12" cy="19" r="1" />
            </svg>
          </button>
        </div>
      </div>
      <div class="project-box-content-header">
        <p class="box-content-header"><?= htmlspecialchars($row->fullname) ?> </p>
        <p class="box-content-subheader"><?= htmlspecialchars($row->goal) ?> </p>
      </div>
      <div class="box-progress-wrapper">
        <p class="box-progress-header">Progress</p>
        <div class="box-progress-bar">
          <span class="box-progress" style="width: 60%; background-color: <?= $spanColor ?>"></span>
        </div>
        <p class="box-progress-percentage">60%</p>
        <p class="box-progress-percentage">2 days left</p>
      </div>
      <div class="project-box-footer">
        <a href="#open-modal-message" class="days-left" id="sendMessage" style="color: <?= $spanColor ?>;">
          Envoyer un message
        </a>
      </div>
    </div>
  </div>

<?php endforeach;
