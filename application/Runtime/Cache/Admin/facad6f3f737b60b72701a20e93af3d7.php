<?php if (!defined('THINK_PATH')) exit();?><style>
.rules{vertical-align: top;}
.rules_label{ margin-right:8px;}
</style>
<div class="modal fade" id="rules-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="rules-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true">
					&times;
				</button>
				权限列表
			</div>
			<div class="modal-body">
				<div id="box-rules">
					<ul>
                        <?php if(is_array($list)): foreach($list as $key=>$v): ?><li>
                            <label class="rules_label"><input type="checkbox" class="rules" name="rules" <?php if(in_array($v['id'], $rules)) echo 'checked'; ?> value="<?php echo ($v["id"]); ?>"><?php echo ($v["title"]); ?></label>
                            <?php if($v['son']){ ?>
                                <div style="padding-left:30px;">
                                    <?php if(is_array($v["son"])): foreach($v["son"] as $key=>$v1): ?><label class="rules_label"><input type="checkbox" class="rules" name="rules" <?php if(in_array($v1['id'], $rules)) echo 'checked'; ?> value="<?php echo ($v1["id"]); ?>"><?php echo ($v1["title"]); ?></label><br>
                                        <?php if($v1['son']){ ?>
                                            <div style="padding-left:30px;">
                                                <?php if(is_array($v1["son"])): foreach($v1["son"] as $key=>$v2): ?><label class="rules_label"><input type="checkbox" class="rules" name="rules" <?php if(in_array($v2['id'], $rules)) echo 'checked'; ?> value="<?php echo ($v2["id"]); ?>"><?php echo ($v2["title"]); ?></label><?php endforeach; endif; ?>
                                            </div>
                                        <?php } endforeach; endif; ?>
                                </div>
                            <?php } ?>
                        </li><?php endforeach; endif; ?>
                    </ul>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-sm btn-danger pull-left" id="choose-all">
					全选
				</button>
				
				<button class="btn btn-sm btn-danger pull-left" id="choose">
					确定
				</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$("#choose-all").click(function(){
		$("#box-rules input[type='checkbox']").each(function(i,n){
			var ischeck = $(n).prop("checked");
			if(ischeck){
				$(n).prop("checked",false);
			} else {
				$(n).prop("checked",true);
			}
		});
	});
	
	$("#choose").click(function(){
		var ids = '';
		$("#box-rules input:checked").each(function(i,n){
			if(i==0){
				ids += $(n).val();
			} else {
				ids += ',' + $(n).val();
			}
		});
		$("#rules").val(ids);
		$("#rules-modal").modal('hide');
	});
});
</script>