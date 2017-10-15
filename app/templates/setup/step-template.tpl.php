<h2><?php echo $lang['templateLayouts']; ?></h2>

<?php if (!$layouts) { ?>

    <p><?php printf($lang['templateNone'], $folder); ?></p>
    <p><?php echo $lang['templateNoneCopy']; ?></p>
    <p><a id="scan-template-again" href="#"><?php echo $lang['templateScanAgain']; ?></a></p>
    
    <script>
        $(document).ready(function(){
            $('#scan-template-again').click(function(){
                setup.loadStep('template');
            });
        });
    </script>

<?php return; } ?>

<p><?php echo $lang['templateLayoutsHint']; ?></p>

<form id="step-template">
    <table>
        <thead>
            <tr>               
                <th colspan="2"><?php echo $lang['templateLayoutFile']; ?></th>
                <th><?php echo $lang['templateLayoutName']; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($layouts as $file=>$name) { ?>
                <tr class="layout-row checked">
                    <td class="check">
                        <input type="checkbox" value="<?php echo $file; ?>">                         
                        <i class="fa fa-check-square-o"></i>
                    </td>
                    <td class="file">                        
                        <?php echo $file; ?>
                    </td>
                    <td class="name">
                        <input type="text" value="<?php echo $name; ?>">
                    </td>
                </tr>
            <?php } ?>
            <tr class="layout-index">
                <td colspan="2">
                    <label for="index_layout">
                        <?php echo $lang['templateLayoutFront']; ?>:
                    </label>
                </td>
                <td>
                    <?php 
                        $index = false;
                        $possible = array('index', 'main', 'frontpage', 'front');
                        foreach($possible as $variant){
                            if (isset($layouts[$variant.'.html'])){
                                $index = $variant.'.html';
                                break;
                            }
                            if (isset($layouts[$variant.'.htm'])){
                                $index = $variant.'.htm';
                                break;
                            }
                        }
                    ?>
                    <select name="index_layout" id="index_layout">
                        <?php foreach ($layouts as $file=>$name) { ?>
                            <option value="<?php echo $file; ?>"<?php if ($index==$file) { ?> selected="selected"<?php } ?>><?php echo $file; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" id="layoutFiles" name="layout_files" value="<?php echo implode("\n", array_keys($layouts)); ?>">
    <input type="hidden" id="layoutNames" name="layout_names" value="<?php echo implode("\n", $layouts); ?>">
</form>
<script>
    $(document).ready(function(){
        $('#step-template .layout-row .file').click(function(){
            var row = $(this).parent('tr');
            rowClick(row);
        });
        $('#step-template .layout-row .check').click(function(){
            var row = $(this).parent('tr');
            rowClick(row);
        });
        $('#step-template .layout-row input:text').change(function(){
            updateLayoutsLists();
        });
        function rowClick(row){
            if (row.hasClass('checked') && $('#step-template .checked').length==1){ return; }
            row.toggleClass('checked');
            $('input:checkbox', row).prop('checked', row.hasClass('checked'));
            $('input:text', row).prop('disabled', !row.hasClass('checked'));
            $('.check i', row).toggleClass('fa-check-square-o').toggleClass('fa-square-o');
            updateLayoutsLists();
        }
        function updateLayoutsLists(){
            var files = [];
            var names = [];
            var indexSelect = $('#step-template select#index_layout');            
            $('#step-template .layout-row').each(function(){
                var row = $(this);
                var file = $('input:checkbox', row).val();
                
                if (!row.hasClass('checked')) { 
                    if ($('option[value="'+file+'"]', indexSelect).length > 0){
                       $('option[value="'+file+'"]', indexSelect).remove();
                    }
                    return; 
                }
                
                files.push(file);
                names.push($('input:text', row).val());
                
                if ($('option[value="'+file+'"]', indexSelect).length == 0){
                    indexSelect.append('<option value="'+file+'">'+file+'</option>');
                }
                
            });
            $('#step-template #layoutFiles').val(files.join("\n"));
            $('#step-template #layoutNames').val(names.join("\n"));
        }
    });
</script>
