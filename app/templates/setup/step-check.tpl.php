<h2><?php echo $lang['serverCheck']; ?></h2>

<table>
    <tbody>
        <?php foreach ($reqs as $row) { ?>
            <tr>
                <td<?php if (!empty($row['nested'])){ ?> class="nested"<?php } ?>>
                    <?php echo $row['title']; ?>
                </td>
                <td>
                    <?php if ($row['isValid']) { ?>
                        <span class="yes">
                            <i class="fa fa-check"></i> <?php echo $lang['checkYes']; ?>
                        </span>
                    <?php } else { ?>
                        <span class="no">
                            <i class="fa fa-times"></i> <?php echo $lang['checkNo']; ?>
                        </span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        <?php if ($errors){ ?>
            <tr>
                <td colspan="2">
                    <a class="b-recheck" href="#recheck"><?php echo $lang['checkAgain']; ?></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<script>
    $(document).ready(function(){
        $('a.b-recheck').click(function(){
            setup.loadStep('check', true);
        });
    });
</script>