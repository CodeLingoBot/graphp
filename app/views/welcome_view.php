<html>
  <head>
    <title>Welcome page</title>
    <style type="text/css">
      .box {
        border: solid black 1px;
        width: 500px;
        margin:100px auto 0 auto;
        padding: 10px;
        text-align: center;
      }
      .header {
        color: blue;
      }
    </style>
  </head>
  <body>
    <div class="box">
      <h2 class="header">Welcome to the GraPHP framework</h2>
      <div>
        This is a work in progress, please
        <a href="https://github.com/mikeland86/graphp">contribute</a>
      </div>
      <? if (!empty($data['arg1'])): ?>
        <div>
          arg1 is <?= $data['arg1'] ?>
        </div>
      <? endif; ?>
    </div>
  </body>
</html>