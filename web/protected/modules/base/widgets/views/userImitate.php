<form method="post" action="">
<?php echo CHtml::dropDownList('UserImitateWidget[user_id]', $user->id, $userList, array('onchange'=>'$(this).parent().submit();')); ?>
</form>