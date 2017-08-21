<?php if (!defined('THINK_PATH')) exit();?><aside class="Hui-aside">
	<input runat="server" id="divScrollValue" type="hidden" value="" />
	<div class="menu_dropdown bk_2">
		<?php if(is_array($nav)): $key = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><dl id="menu-article">
			<dt menu_key="<?php echo ($key); ?>"><i class="Hui-iconfont"><?php echo ((isset($vo["icon"]) && ($vo["icon"] !== ""))?($vo["icon"]):"&#xe616;"); ?></i> <?php echo ($vo["title"]); ?><i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd class="hide  _menu<?php echo ($key); ?>">
				<ul>
					<?php if(is_array($vo['menu'])): $i = 0; $__LIST__ = $vo['menu'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U($k['name']);?>" title="<?php echo ($k["title"]); ?>"><?php echo ($k["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</dd>
		</dl><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>