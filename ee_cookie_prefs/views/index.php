<style>
.prefixed-input {
	font-size:12px;padding:0 0 0 3px; border:1px solid #999;border-color: #b3b3b3 #cdcdcd #cdcdcd #b3b3b3;display:inline-block;color:#999;background:#fff;border-radius:3px
}
.prefixed-input .input {
	display:inline-block;padding:0 0 0 3px
}
.prefixed-input .input input {
	border:none;
	padding: 6px 5px 7px;
}
</style>

<div class="box">

<h1><?= $cp_page_title; ?></h1>

<?=form_open($base_url, array('class' => 'settings', 'id' => 'ee_cookie_prefs_form'));?>


<p><?= lang('ee_cookie_prefs_desc') ?>:<br><small style="color:#999;"><?= lang('ee_cookie_prefs_note') ?></small></p>

<?php
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    lang('cookie_name'),
    lang('expires'),
    lang('domain'),
    lang('path'),
    lang('secure_cookie'),
    lang('httponly'),
    lang('samesite'),
    lang('remove')
);

if ( ! empty($cookies))
{
	foreach ($cookies as $key => $val)
	{
		if ( ! empty($val))
		{		
			$fields = array();
			$fields[] = form_input("cookies[{$key}][name]", $val['name']);
			$fields[] = form_input("cookies[{$key}][expires]", $val['expires']);
			$fields[] = form_input("cookies[{$key}][domain]", $val['domain']);
			$fields[] = form_input("cookies[{$key}][path]", $val['path']);
			$fields[] = form_dropdown("cookies[{$key}][secure_cookie]", array('' => '', 'y' => lang('yes'), 'n' => lang('no')), $val['secure_cookie']);
			$fields[] = form_dropdown("cookies[{$key}][httponly]", array('' => '', 'y' => lang('yes'), 'n' => lang('no')), $val['httponly']);
			$fields[] = form_dropdown("cookies[{$key}][samesite]", array('' => '', 'Lax' => lang('Lax'), 'Strict' => lang('Strict'), 'None' => lang('None')), $val['samesite']);
			$fields[] = '<a class="icon--delete remove"></a>';
			
			$this->table->add_row($fields);
		}
	}
}

echo $this->table->generate();

$this->table->clear();

?>

<div><small style="color:#999;"><?= lang('example_cookie_name') ?>: <?= $cookie_prefix ?>last_visit</small></div>

<br>
<br>
<h2><?= lang('other_options') ?></h2>

<p><?= lang('other_options_desc') ?></p>

<?php
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    lang('preference'),
    lang('setting')
);

$this->table->add_row(array(
	lang('samesite_none_fix') . ':<br><small style="color:#999;">' . lang('samesite_none_fix_desc') . '</small>',
	form_dropdown('samesite_none_fix', array('y' => lang('yes'), 'n' => lang('no')), (isset($samesite_none_fix) ? $samesite_none_fix : 'n'))
));

echo $this->table->generate();

$this->table->clear();

?>

<p><?= lang('cookie_consent_desc') ?></p>


<?php
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    lang('preference'),
    lang('setting')
);

$this->table->add_row(array(
	lang('enable_consent_cookies') . ':<br><small style="color:#999;">' . lang('enable_consent_cookies_desc') . '</small>',
	form_dropdown('enable_consent_cookies', array('n' => lang('no'), 'y' => lang('yes')), (isset($enable_consent_cookies) ? $enable_consent_cookies : 'n'))
));
$this->table->add_row(array(
	lang('enable_consent_cookies_name') . ':<br><small style="color:#999;">' . lang('enable_consent_cookies_name_desc') . '</small>',
	'<div class="prefixed-input">'.$cookie_prefix.'<div class="input">'.
		form_input('enable_consent_cookies_name', (isset($enable_consent_cookies_name) ? $enable_consent_cookies_name : '')).
	'</div></div>'
));
$this->table->add_row(array(
	lang('enable_consent_cookies_format') . ':<br><small style="color:#999;">' . lang('enable_consent_cookies_format_desc') . '</small>',
	form_dropdown('enable_consent_cookies_format', array('' => lang('default'), 'json' => lang('json'), 'serialize' => lang('serialize'), 'comma_separated_values' => lang('comma_separated_values')), (isset($enable_consent_cookies_format) ? $enable_consent_cookies_format : 'n'))
));

echo $this->table->generate();

$this->table->clear();

?>

<div class="form-btns">			
	<input class="btn" type="submit" value="Save Settings" data-submit-text="Save Settings" data-work-text="Saving...">
</div>

<?=form_close()?>
  
</div>


<?php
/* End of file index.php */
