<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" charset="utf-8" src="/assets/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="/assets/ueditor/lang/zh-cn/zh-cn.js"></script>
<div style="width:100%">
    <textarea id="myEditor" name="<?php echo ($id); ?>" type="text/plain" style="width:100%;height:350px">
<?php echo ($content); ?>
</textarea>
</div>
<script type="text/javascript">
    var editor = new UE.ui.Editor();
    editor.render("myEditor");
</script>