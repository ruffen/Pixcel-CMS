                        <tr class="<?php echo $alter;?> <?php echo (isset($selected)) ? $selected : '';?>" id="file_<?php echo $id;?>">
<?php
if($checkbox){
?>            
			                <td><input type="checkbox" name="files" class="filecheckbox" value="filebox_<?php echo $id;?>" /></td>
<?php
}
?>
                            <td><div class="imgholder"><img src="<?php echo $source;?>" class="preview" /></div></td>
                            <td><?php echo $extension;?></td>
                            <td><?php echo $filename;?></td>
                            <td><?php echo $timestamp;?></td>
                            <td><?php echo $size;?>kb</td>
                            <td style="text-align:center;"><img src="/static/images/icons/24/repeat.png" align="middle" /></td>
                        </tr>