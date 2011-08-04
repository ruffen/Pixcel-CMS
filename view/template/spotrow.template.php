                            <tr id="spot_<?php echo $index;?>" rel="<?php echo $tplSpot->getTplSpotId();?>">
								<td width="35%" class="greytd"><img src="/static/images/icons/24/windows.png" alt="" class="site" />
									<span class="spotname"><?php echo $tplSpot->getName();?></span>&nbsp;
									<span class="spottype">(<?php echo $tplSpot->getType();?>)</span>
								</td>
                                <td>
                                	<span class="tblcheck">
                                        <select name="contentregion" class="spottype" rel="<?php echo $tplSpot->getTplSpotId();?>">
                                        	<option value="0" >Select...</option>
<?php
	foreach($spots as $spot){
?>
                                            <option value="<?php echo $spot->getId();?>" <?php echo ($tplSpot->getId() == $spot->getId()) ? 'selected="selected"' : '';?>><?php echo $spot->getType();?></option>
<?php
	}
?>
                                        </select>
                                    </span>
                                </td>
                                <td width="5%" rel="<?php echo $tplSpot->getTplSpotId();?>" class="configcontainer"><a href="#" class="spotconfig <?php echo $showSpotconfig;?>" rel="<?php echo $tplSpot->getId();?>"><img src="/static/images/icons/24/process.png" align="left" class="site" /></a></td>
                            </tr>