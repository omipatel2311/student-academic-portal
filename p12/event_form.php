<?php if($errors):?><div class="alert error"><ul><?php foreach($errors as $er):?><li><?=e($er)?></li><?php endforeach;?></ul></div><?php endif;?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=e(csrf())?>">
<label>Event title<input name="title" maxlength="150" required value="<?=e($event["title"]??"")?>"></label>
<label>Description<textarea name="description" rows="4" required><?=e($event["description"]??"")?></textarea></label>
<div class="grid-two"><label>Event date<input type="date" name="event_date" required value="<?=e($event["event_date"]??date("Y-m-d"))?>"></label><label>Venue<input name="venue" maxlength="180" required value="<?=e($event["venue"]??"")?>"></label></div>
<div class="grid-two"><label>Category<select name="category"><?php $cats=["Workshop","Seminar","Meeting","Exhibition","Competition","Cultural","Sports","General"];foreach($cats as $c):?><option <?=($event["category"]??"Workshop")===$c?"selected":""?>><?=e($c)?></option><?php endforeach;?></select></label><label>Status<select name="status"><option <?=($event["status"]??"Open")==="Open"?"selected":""?>>Open</option><option <?=($event["status"]??"")==="Closed"?"selected":""?>>Closed</option></select></label></div>
<label>Poster <span class="muted small">(optional: JPG, PNG, WEBP; max 2 MB)</span><input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
<?php if(!empty($event["poster"])):?><img class="preview" src="uploads/<?=rawurlencode($event["poster"])?>" alt="Current poster"><?php endif;?>
<div class="form-actions"><button class="btn"><?= $isEdit?"Save Changes":"Create Event" ?></button><a class="btn secondary" href="index.php">Cancel</a></div></form>
