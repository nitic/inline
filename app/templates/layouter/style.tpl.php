<?php 
    use InlineCMS\Core\Lang; 
?>
<form action="#" method="post" class="inlinecms">

    <fieldset>

        <div class="field f-size">
            <label><?php echo Lang::get('styleSize'); ?>:</label>
            <div class="fields-small">
                <label><?php echo Lang::get('styleSizeWidth'); ?>: <input type="text" name="width" class="b-width"></label>
                <label><?php echo Lang::get('styleSizeHeight'); ?>: <input type="text" name="height" class="b-height"></label>
            </div>           
        </div>
        
        <div class="field f-padding">
            <label><?php echo Lang::get('stylePadding'); ?>:</label>
            <div class="fields-small">
                <label><?php echo Lang::get('styleTop'); ?>: <input type="text" name="paddingTop" class="b-top"></label>                
                <label><?php echo Lang::get('styleLeft'); ?>: <input type="text" name="paddingLeft" class="b-left"></label>
                <label><?php echo Lang::get('styleBottom'); ?>: <input type="text" name="paddingBottom" class="b-bottom"></label>
                <label><?php echo Lang::get('styleRight'); ?>: <input type="text" name="paddingRight" class="b-right"></label>
            </div>           
        </div>
        
        <div class="field f-margin">
            <label><?php echo Lang::get('styleMargin'); ?>:</label>
            <div class="fields-small">
                <label><?php echo Lang::get('styleTop'); ?>: <input type="text" name="marginTop" class="b-top"></label>                
                <label><?php echo Lang::get('styleLeft'); ?>: <input type="text" name="marginLeft" class="b-left"></label>
                <label><?php echo Lang::get('styleBottom'); ?>: <input type="text" name="marginBottom" class="b-bottom"></label>
                <label><?php echo Lang::get('styleRight'); ?>: <input type="text" name="marginRight" class="b-right"></label>
            </div>
        </div>

    </fieldset>
    
</form>
