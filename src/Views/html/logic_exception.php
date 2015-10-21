<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $header; ?></title>

    <!-- Bootstrap CSS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>

    <style type="text/css">
        @charset "utf-8";
        /* CSS Document */
        body {
            font-family: 'Roboto';
            font-size: 12px;
        }

        .error-wrapper {
            font-family: 'Roboto';
            font-size: 12px;
            color:#303641;
            margin: 50px;
        }


        * {
            margin: 0px;
            padding: 0px;
        }

        a {
            text-decoration: none !important;
        }

        h1 {
            font-size: 28px;
            color: #e73d2f;
            text-transform: uppercase;
            padding: 20px 0px 0px 0px;
        }

        h2 {
            font-size: 16px;
            text-transform: uppercase;
        }

        .error-text {
            color: #e73d2f;
            font-weight: 800;
        }

        p {
            font-size: 14px;
            padding: 10px 0px;
            font-weight: 400;
        }

        .copyright {
            font-weight: 400;
            font-size: 10px;
            text-transform: uppercase;
        }

        small {
            font-size: 8px;
            text-transform: uppercase;
        }

        table {
            margin:10px 0px;
            font-size: 11px;
        }

        table td {
            padding: 5px 0px;
        }
    </style>
</head>
<body>
    <div class="error-wrapper">

        <img src="data:image/png;base64,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" height="50"/>

        <h1><?php echo $header; ?></h1>

        <?php if(isset($description)): ?>
        <small><?php echo $description; ?></small>
        <?php endif; ?>

        <table>
            <tr>
                <td colspan="3"><h3><?php echo $exception->getMessage(); ?></h3></td>
            </tr>
            <tr>
                <td>Filename</td>
                <td>:</td>
                <td><?php echo $exception->getFile(); ?></td>
            </tr>
            <tr>
                <td>Line Number</td>
                <td>:</td>
                <td><?php echo $exception->getLine(); ?></td>
            </tr>
            <tr>
                <td valign="top">Trace</td>
                <td valign="top">:</td>
                <td valign="top">
                    <ol style="margin:1px 15px 15px 15px;">
                    <?php if(isset($tracer)): ?>
                        <?php foreach ($tracer->chronology() as $chronology): ?>

                            <li style="padding-bottom: 5px;">
                                <?php echo $chronology->call; ?><br>
                                <?php echo 'File: '.@realpath($chronology->file); ?><br>
                                <?php echo 'Line: '.$chronology->line; ?>
                            </li>

                        <?php endforeach ?>
                    <?php endif; ?>
                    </ol>
                </td>
            </tr>
        </table>

        <div class="copyright">
            O2System Glob (O2Glob)<br/>
            <small>Global PHP Libraries</small><br/><br/>

            <small>
                Copyright &copy; 2010 - <?php echo date('Y'); ?><br>
                PT. Lingkar Kreasi (Circle Creative)<br>
                All Rights Reserved
            </small>
        </div>
    </div>
</body>
</html>