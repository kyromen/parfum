<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.6.2
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2015 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<script type="text/javascript">
<!--
Joomla.submitbutton = function(task)
{
    if( task == 'cancel' ) {
        Joomla.submitform(task);
        return;
    }
    // Create the filters array
    var txt = '';
    for( var i = 0, n = filters.length; i < n; i++ ) {
        if( i > 0 ) {
            txt += '\n';
        }
        txt += filters[i][0] + '=';
        for( var j = 1, m = filters[i].length; j < m; j++ ) {
            if( j > 1 ) {
                txt += ',';
            }
            txt += filters[i][j];
        }
    }
    // Set the value and send the form
    document.adminForm.filters.value = txt;
    Joomla.submitform(task);
}

var filters = new Array();
var acceptVars = new Array();

<?php
// Create the arrays of variable filter rules
if( count($this->acceptVars) > 0 ) {
    $i = 0;
    foreach($this->acceptVars as $acceptVar) {
        echo "acceptVars[{$i}] = '{$acceptVar}';\n";
        $i++;
    }
}

$i = 0;
if( count($this->filters['pos']) > 0 ) {
    foreach($this->filters['pos'] as $filter) {
    	$rule=str_replace("\\",'\\\\',$filter->rule);
        echo "filters[{$i}] = new Array('+{$rule}'";
        foreach($filter->vars as $var) {
            echo ", '{$var}'";
        }
        echo ");\n";
        //echo "alert(filters[{$i}]);";
        $i++;
    }
}
if( count($this->filters['neg']) > 0 ) {
    foreach($this->filters['neg'] as $filter) {
        echo "filters[{$i}] = new Array('-{$filter->rule}'";
        foreach($filter->vars as $var) {
            echo ", '{$var}'";
        }
        echo ");\n";
        $i++;
    }
}
?>

function removeRule()
{
    var el = $('selRules');

    var i = el.selectedIndex;
    if( i < 0 ) {
        return;
    }

    // Remove the option from list
    el.remove(i);

    // Remove the filter from array
    filters.splice(i, 1);

    // Select the correct remaining rule
    if( el.length > 0 ) {
        if( i >= el.length ) {
            i = el.length - 1;
        }
        el.selectedIndex = i;
    }

    ruleClicked();
}

function removeAllRules()
{
    // Confirm
    var q = confirm('<?php echo JText::_('COM_SEF_WARNING_REMOVE_FILTER_ALL_RULES'); ?>');
    if( !q ) {
        return;
    }

    var el = $('selRules');

    // Remove options from list
    el.options.length = 0;

    // Remove all the filters
    filters.length = 0;

    ruleClicked();
}

function addRule()
{
    var re = $('ruleRegExp').value;
    var neg = $('ruleNegate').checked;

    // Check regular expression
    if( re == '' ) {
        alert('<?php echo JText::_('COM_SEF_WARNING_ADD_FILTER_RULE_EMPTY'); ?>');
        return;
    }

    // Check if the rule already exists
    var txt = (neg ? '-' : '+') + re;
    for( var i = 0, n = filters.length; i < n; i++ ) {
        if( filters[i][0] == txt ) {
            alert('<?php echo JText::_('COM_SEF_WARNING_FILTER_RULE_EXISTS'); ?>');
            return;
        }
    }

    // Create new filter in array
    filters.push(new Array(txt));

    // Add the option to list
    var el = $('selRules');
    txt = re;
    if( neg ) {
        txt = 'NOT ' + txt;
    }
    try {
        el.add(new Option(txt, el.length)); // IE, Opera
    }
    catch(e) {
        el.add(new Option(txt, el.length), null); // FF
    }

    // Select new filter
    el.selectedIndex = el.length - 1;
    ruleClicked();
}

function ruleClicked()
{
    var el = $('selRules');
    var assigned = $('assignedVars');

    // Clear the assigned vars list
    assigned.options.length = 0;

    // Add all the assigned variables
    if( el.selectedIndex >= 0 ) {
        for( var i = 1, n = filters[el.selectedIndex].length; i < n; i++ ) {
            try {
                assigned.add(new Option(filters[el.selectedIndex][i], i)); // IE, Opera
            }
            catch(e) {
                assigned.add(new Option(filters[el.selectedIndex][i], i), null); // FF
            }
        }
    }

    showAvailableVars();
}

function showAvailableVars()
{
    var el = $('selRules');
    var available = $('availableVars');

    // Clear the available vars list
    available.options.length = 0;

    // Add the available vars
    var filter = null;

    var ind = el.selectedIndex;
    if( ind >= 0 ) {
        filter = filters[ind];
    }

    for( var i = 0, n = acceptVars.length; i < n; i++ ) {
        if( (filter != null) && (filter.indexOf(acceptVars[i], 1) > 0) ) {
            continue;
        }

        try {
            available.add(new Option(acceptVars[i], i)); // IE, Opera
        }
        catch(e) {
            available.add(new Option(acceptVars[i], i), null); // FF
        }
    }
}

function addAll()
{
    var el = $('selRules');
    if( el.selectedIndex < 0 ) {
    	alert('<?php echo JText::_('COM_SEF_SELECT_RULE'); ?>');
        return;
    }

    var available = $('availableVars');
    var vars = new Array();

    // Get all the available variables
    for( var i = 0, n = available.length; i < n; i++ ) {
        vars.push(available.options[i].text);
    }

    // Add variables
    addVars(vars);
}

function addSelected()
{
    var el = $('selRules');
    if( el.selectedIndex < 0 ) {
    	alert('<?php echo JText::_('COM_SEF_SELECT_RULE'); ?>');
        return;
    }

    var available = $('availableVars');
    var vars = new Array();

    // Get selected available variables
    for( var i = 0, n = available.length; i < n; i++ ) {
        if( available.options[i].selected ) {
            vars.push(available.options[i].text);
        }
    }

    // Add variables
    addVars(vars);
}

function removeSelected()
{
    var el = $('selRules');
    if( el.selectedIndex < 0 ) {
        return;
    }

    var assigned = $('assignedVars');
    var vars = new Array();

    // Get selected assigned variables
    for( var i = 0, n = assigned.length; i < n; i++ ) {
        if( assigned.options[i].selected ) {
            vars.push(assigned.options[i].text);
        }
    }

    // Add variables
    removeVars(vars);
}

function removeAll()
{
    var el = $('selRules');
    if( el.selectedIndex < 0 ) {
        return;
    }

    var assigned = $('assignedVars');
    var vars = new Array();

    // Get all the assigned variables
    for( var i = 0, n = assigned.length; i < n; i++ ) {
        vars.push(assigned.options[i].text);
    }

    // Add variables
    removeVars(vars);
}

function addVars(vars)
{
    var el = $('selRules');
    var ind = el.selectedIndex;
    if( ind < 0 ) {
        return;
    }

    // Get the assigned variables, remove them from filter, and add them to new vars
    for( var i = 0, n = filters[ind].length - 1; i < n; i++ ) {
        vars.push(filters[ind].pop());
    }

    // Sort the variables
    vars.sort();

    // Add them back to filter
    for( var i = 0, n = vars.length; i < n; i++ ) {
        filters[ind].push(vars.shift());
    }

    // Update lists
    ruleClicked();
}

function removeVars(vars)
{
    var el = $('selRules');
    var ind = el.selectedIndex;
    if( ind < 0 ) {
        return;
    }

    // Loop through the vars and remove them from assigned variables
    for( var i = 0, n = vars.length; i < n; i++ ) {
        var pos = filters[ind].indexOf(vars[i], 1);
        if( pos > 0 ) {
            filters[ind].splice(pos, 1);
        }
    }

    // Update lists
    ruleClicked();
}

-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="sef-width-60 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Parameters' ); ?></legend>

        <?php
        echo JHtml::_('tabs.start', 'sef-extension-tabs', array('useCookie' => 1));

        // Render each parameters group
        $fieldsets = $this->extension->form->getFieldsets();
        if (is_array($fieldsets) && count($fieldsets) > 0) {
            $i = 0;
            foreach ($fieldsets as $name => $fieldset) {
                if ($name == 'varfilter') {
                    continue;
                }

                $fields = $this->extension->form->getFieldset($name);
                if (count($fields) > 0) {
                    $label = JText::_($name);
                    $i++;
                    echo JHtml::_('tabs.panel', $label, 'page-'.$i);

                    $this->renderParams($this->extension->form, $name);
                }
            }
        }
        
        echo JHTML::_('tabs.panel',Jtext::_('COM_SEF_SUBDOMAINS'),'subdomains');
        ?>
        <fieldset class="adminform">
        	<legend><?php echo Jtext::_('COM_SEF_SUBDOMAINS'); ?></legend>
        	<table class="adminform table table-striped">
        		<tr>
        			<th>
        			<?php echo Jtext::_('COM_SEF_SUBDOMAIN'); ?>
        			</th>
        			<th>
        			<?php echo Jtext::_('COM_SEF_TITLEPAGE'); ?>
        			</th>
        			<th>
        			<?php echo Jtext::_('COM_SEF_LANGUAGE'); ?>
        			</th>
        		</tr>
        		<?php
        		foreach($this->langs as $lang) {
        			$sef=$lang->sef;
        			?>
        			<tr>
        				<td>
        				<input class="inputbox" type="textbox" size="10" style="text-align:right" name="subdomain[<?php echo $sef; ?>][title]" value="<?php echo @$this->subdomains[$sef]->subdomain; ?>" />.<?php echo $this->rootDomain; ?>
        				</td>
        				<td>
        				<?php echo JHTML::_('select.genericlist',$this->menus[$sef],"subdomain[".$sef."][titlepage]",array('list.select'=>@$this->subdomains[$sef]->Itemid_titlepage)); ?>
        				</td>
        				<td>
        				<?php echo $lang->title; ?>
        				</td>
        			</tr>
        			<?php
        		}
        		?>
        	</table>
        </fieldset>
        <?php

        echo JHtml::_('tabs.panel', JText::_('COM_SEF_VARIABLES_FILTERING'), 'varfilter');
        ?>

        <fieldset class="panelform">
        <div id="filterdiv">
        <table width="100%">
            <tr>
                <th align="left"><?php echo JText::_('COM_SEF_USAGE'); ?></th>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_SEF_DESC_VARIABLE_FILTER_USAGE'); ?></td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <th align="left" colspan="2"><?php echo JText::_('COM_SEF_ADD_RULE'); ?></th>
            </tr>
            <tr>
                <td>
                	<?php echo JText::_('COM_SEF_REGULAR_EXPRESSION'); ?>:
                    <input type="text" name="ruleRegExp" id="ruleRegExp" value="" size="25" style="float:none" />
                    <?php echo JText::_('COM_SEF_NEGATE_THIS_RULE'); ?>
                    <input type="checkbox" name="ruleNegate" id="ruleNegate" style="float:none" />
                    <input type="button" class="btn btn-small" value="<?php echo JText::_('COM_SEF_ADD_RULE'); ?>" onclick="addRule();" style="float:none" />
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo JText::_('COM_SEF_DESC_VARIABLE_FILTER_REGEXP'); ?>
                </td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <th align="left" width="40%"><?php echo JText::_('COM_SEF_RULES'); ?></th>
                <th align="left" width="25%"><?php echo JText::_('COM_SEF_ASSIGNED_VARIABLES'); ?></th>
                <th align="left" width="10%">&nbsp;</th>
                <th align="left" width="25%"><?php echo JText::_('COM_SEF_AVAILABLE_VARIABLES'); ?></th>
            </tr>
            <tr>
                <td>
                    <select name="selRules" id="selRules" size="10" onchange="ruleClicked();" style="width: 90%;">
                        <?php
                        // Create options for rules
                        $i = 0;
                        if( count($this->filters['pos']) > 0 ) {
                            foreach($this->filters['pos'] as $filter) {
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $filter->rule; ?></option>
                                <?php
                                $i++;
                            }
                        }
                        if( count($this->filters['neg']) > 0 ) {
                            foreach($this->filters['neg'] as $filter) {
                                ?>
                                <option value="<?php echo $i; ?>">NOT <?php echo $filter->rule; ?></option>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <select name="assignedVars" id="assignedVars" size="10" multiple="multiple" ondblclick="removeSelected();" style="width: 100%;">
                    </select>
                </td>
                <td align="center">
                    <input class="hasTip btn btn-small" title="<?php echo JText::_('COM_SEF_TT_ADD_ALL_VARIABLES'); ?>" type="button" value="&lt;&lt;" onclick="addAll();" style="margin: 5px; float:none;" /><br />
                    <input class="hasTip btn btn-small" title="<?php echo JText::_('COM_SEF_TT_ADD_SELECTED_VARIABLES'); ?>" type="button" value="&lt;" onclick="addSelected();" style="margin: 5px; float:none;" /><br />
                    <input class="hasTip btn btn-small" title="<?php echo JText::_('COM_SEF_TT_REMOVE_SELECTED_VARIABLES'); ?>" type="button" value="&gt;" onclick="removeSelected();" style="margin: 5px; float:none;" /><br />
                    <input class="hasTip btn btn-small" title="<?php echo JText::_('COM_SEF_TT_REMOVE_ALL_VARIABLES'); ?>" type="button" value="&gt;&gt;" onclick="removeAll();" style="margin: 5px; float:none;" />
                </td>
                <td>
                    <select name="availableVars" id="availableVars" size="10" multiple="multiple" ondblclick="addSelected();" style="width: 100%;">
                        <?php
                        // Create options for accept vars
                        $i = 0;
                        if( count($this->acceptVars) > 0 ) {
                            foreach($this->acceptVars as $var) {
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $var; ?></option>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <input type="button" class="btn btn-small" value="<?php echo JText::_('COM_SEF_REMOVE_SELECTED_RULE'); ?>" onclick="removeRule();" />
                    <input type="button" class="btn btn-small" value="<?php echo JText::_('COM_SEF_REMOVE_ALL_RULES'); ?>" onclick="removeAllRules();" />
                </td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <th align="left"><?php echo JText::_('COM_SEF_OPTIONS'); ?></th>
            </tr>
        </table>
        <?php
        $this->renderParams($this->extension->form, 'varfilter');
        ?>
        </div>
        </fieldset>
        <?php
        if(count($this->strings)>0) {
			echo JHtml::_('tabs.panel', JText::_('COM_SEF_TEXTS'), 'texts');
			echo JHTML::_('tabs.start','sef-extension-texts');
			echo JHTML::_('tabs.panel',JText::_('COM_SEF_Default'),'default');
			?>
			<fieldset class="adminform">
				<table class="adminlist">
				<tr>
					<th>
					<?php echo JText::_('COM_SEF_TEXT_NAME'); ?>
					</th>
					<th>
					<?php echo JText::_('COM_SEF_TEXT_VALUE'); ?>
					</th>
				</tr>
					<?php
					for($j=0;$j<count($this->strings);$j++) {
						$name=$this->strings[$j]->name;
						?>
							<tr>
								<td>
								<?php echo $name; ?>
								</td>
								<td>
								<input class="inputbox" type="text" size="50" value="<?php echo $this->translation[0][$name]; ?>" name="texts[0][<?php echo $name; ?>];"/>
								</td>
							</tr>
						<?php
					}
					?>
				</table>
			</fieldset>
			<?php
			for($i=0;$i<count($this->langs);$i++) {
				//echo JHTML::_('tabs.panel',JHTML::_('image','../media/mod_languages/images/'.$this->langs[$i]->image.'.gif',$this->langs[$i]->code)."&nbsp;".$this->langs[$i]->code,$this->langs[$i]->code);
                echo JHTML::_('tabs.panel','<img src=../media/mod_languages/images/'.$this->langs[$i]->image.'.gif alt="'.$this->langs[$i]->lang_code.'"/>&nbsp;'.$this->langs[$i]->lang_code,$this->langs[$i]->lang_code);
				?>
				<fieldset class="adminform">
					<table class="adminlist">
						<tr>
							<th>
							<?php echo JText::_('COM_SEF_TEXT_NAME'); ?>
							</th>
							<th>
							<?php echo JText::_('COM_SEF_TEXT_VALUE'); ?>
							</th>
						</tr>
						<?php
						for($j=0;$j<count($this->strings);$j++) {
							$name=$this->strings[$j]->name;
							?>
								<tr>
									<td>
									<?php echo $this->strings[$j]->name; ?>
									</td>
									<td>
                                    <input class="inputbox" type="text" size="50" value="<?php echo @$this->translation[$this->langs[$i]->lang_id][$name]; ?>" name="texts[<?php echo $this->langs[$i]->lang_id; ?>][<?php echo $name; ?>]"/>
									</td>
								</tr>
							<?php
						}
						?>
					</table>
				</fieldset>
				<?php
			}
			echo JHTML::_('tabs.end');
        }
		?>
        <?php
        echo JHtml::_('tabs.end');
        ?>
    </fieldset>
</div>

<div class="sef-width-40 fltrt">
    <?php
    if( !empty($this->extension->name) ) {
        ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Extension Details' ); ?></legend>

            <table class="adminlist table">
                <tr>
                    <th width="150">
                        <?php echo JText::_('COM_SEF_NAME'); ?>:
                    </th>
                    <td>
                        <?php echo $this->extension->name; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php echo JText::_('COM_SEF_VERSION'); ?>:
                    </th>
                    <td>
                        <?php echo $this->extension->version; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php echo JText::_('COM_SEF_DESCRIPTION'); ?>:
                    </th>
                    <td>
                        <?php echo $this->extension->description; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php
    }
    ?>

    <?php
    if( !is_null($this->extension->component) ) {
        ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Component Details' ); ?></legend>

            <table class="adminlist table">
                <tr>
                    <th width="150">
                        <?php echo JText::_('COM_SEF_NAME'); ?>:
                    </th>
                    <td>
                        <?php echo $this->extension->component->name; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php echo JText::_('COM_SEF_OPTION'); ?>:
                    </th>
                    <td>
                        <?php echo $this->extension->component->option; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php
    }
    ?>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="controller" value="extension" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="element" value="<?php echo $this->extension->id; ?>" />
<input type="hidden" name="redirto" value="<?php echo $this->redirto; ?>" />
<input type="hidden" name="filters" value="" />

<?php echo JHTML::_( 'form.token' ); ?>
</form>
