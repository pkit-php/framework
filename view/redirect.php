<?php

use Pkit\Utils\View;

$_ARGS = View::getArgs()
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="refresh" content="0;url=<?= $_ARGS['site'] ?? '/' ?>" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $_ARGS['title'] ?? 'redirect' ?></title>
</head>

<body>
  <div class="center">
    <svg width="128" height="128" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
      <path d="M64 128C80.9739 128 97.2525 121.257 109.255 109.255C121.257 97.2525 128 80.9739 128 64L106.249 64C106.249 75.2052 101.798 85.9515 93.8748 93.8748C85.9515 101.798 75.2052 106.249 64 106.249L64 128Z" />
      <path d="M64 0C55.5954 -1.00224e-07 47.2731 1.65541 39.5083 4.87171C31.7434 8.08801 24.6881 12.8022 18.7452 18.7452C12.8022 24.6881 8.08801 31.7434 4.87171 39.5083C1.65541 47.2731 -1.26906e-06 55.5954 0 64L21.7507 64C21.7507 58.4517 22.8435 52.9578 24.9667 47.8319C27.09 42.706 30.202 38.0484 34.1252 34.1252C38.0484 30.202 42.706 27.09 47.8319 24.9667C52.9578 22.8435 58.4517 21.7507 64 21.7507L64 0Z" />
    </svg>
  </div>
  <style>
    :root {
      --color: <?= $_ARGS['color'] ?? "#f22" ?>
    }

    body {
      margin: 0;
      padding: 0;
    }

    .center {
      width: 100%;
      height: 100vh;
    }

    .center,
    .icons {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    @keyframes loading {
      to {
        transform: rotateZ(0deg);
      }

      from {
        transform: rotateZ(720deg);
      }
    }

    svg {
      fill: var(--color);

      animation-name: loading;
      animation-duration: 4s;
      animation-iteration-count: infinite;
      /* animation-timing-function: linear; */
    }
  </style>
</body>

</html>