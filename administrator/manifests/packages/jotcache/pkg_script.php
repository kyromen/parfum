<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */

class Pkg_JotcacheInstallerScript {

  private $app;

  function preflight($type, $parent) {
    $this->app = JFactory::getApplication();
  }

  function install() {
    $lang = JFactory::getLanguage();
    $lang->load('com_jotcache.sys');
    $lang->load('com_jotcache');
    $dbChangeMessages = array();
    /* @var $db JDatabase */
    $db = JFactory::getDBO();
    $ret = $this->jotcacheUpgrade($db);
    if ($ret) {
      $dbChangeMessages[] = $ret;
    }
    $dbname = $db->name;
    switch ($dbname) {
      case 'sqlsrv':
      case 'sqlzure':
        $items = array("jotcache,cookies,NVARCHAR(2000) NOT NULL",
            "jotcache,domain,NVARCHAR(255) NOT NULL",
            "jotcache,recache,tinyint",
            "jotcache,recache_chck,tinyint",
            "jotcache,agent,tinyint"
        );
        foreach ($items as $item) {
          $table = "";
          $column = "";
          $type = "";
          list($table, $column, $type) = explode(",", $item, 3);
          $sql = $db->getQuery(true);
          $prefix = $db->getPrefix();
          $sql->select('column_name')
                  ->from('information_schema.columns')
                  ->where('table_name = ' . $db->quote($prefix . $table))
                  ->where('column_name =' . $db->quote($column));
          $res = $db->setQuery($sql)->loadResult();
//        echo 'res:'.$db->getQuery();
          if (!$res) {
            $query = "ALTER TABLE " . $prefix . $table . " ADD " . $column . " " . $type;
            $db->setQuery($query);
            $dbChangeMessages[] = substr($db->getQuery(), 15);
            $db->query();
          }
        }
        break;
// covers both db systems : mysql, mysqli   
      default:
        $items = array("jotcache,mark,TINYINT(1)",
            "jotcache,title,varchar(255) NOT NULL AFTER `fname`",
            "jotcache,domain,varchar(255) NOT NULL AFTER `fname`",
            "jotcache,uri,TEXT NOT NULL AFTER `title`",
            "jotcache,recache,TINYINT(1) NOT NULL AFTER `mark`",
            "jotcache,recache_chck,TINYINT(1) NOT NULL AFTER `recache`",
            "jotcache,agent,TINYINT(1) NOT NULL AFTER `recache_chck`",
            "jotcache,language,VARCHAR(5) NOT NULL AFTER `uri`",
            "jotcache,browser,VARCHAR(50) NOT NULL AFTER `language`",
            "jotcache,qs,TEXT NOT NULL",
            "jotcache,cookies,TEXT NOT NULL",
            "jotcache_exclude,type,TINYINT(4) NOT NULL"
        );
        foreach ($items as $item) {
          $table = "";
          $column = "";
          $type = "";
          list($table, $column, $type) = explode(",", $item, 3);
          $sql = "DESCRIBE `#__" . $table . "` `" . $column . "`";
          $db->setQuery($sql);
          if (!$db->loadResult()) {
            $sql = "ALTER TABLE #__" . $table . " ADD `" . $column . "` " . $type . ";";
            $db->setQuery($sql);
            $dbChangeMessages[] = substr($db->getQuery(), 15);
            $db->query();
          }
        }
        break;
    }
    ?>
    <?php if (count($dbChangeMessages)) { ?>
      <h3><?php echo JText::_('PKG_JOTCACHE_ALTER_DB'); ?></h3>
      <table class="table table-striped">
        <tbody>
          <?php
          $k = 0;
          foreach ($dbChangeMessages as $msg) {
            ?>
            <tr class="row<?php echo $k; ?>" >
              <td class="key" colspan="3" style="border-bottom:1px solid #DDDDDD;"><?php echo $msg; ?></td>
            </tr>
            <?php
            $k = 1 - $k;
          }
          ?>
        </tbody>
      </table>
      <?php
    }
    return true;
  }

  function jotcacheUpgrade(JDatabase $db) {
    $message = '';
    $query = $db->getQuery(true);
    $query->select('COUNT(*)')
            ->from('#__jotcache_exclude')
            ->where('type=1');
    $tplexCount = $db->setQuery($query)->loadResult();
    if ($tplexCount == 0) {
      return false;
    }
    $query->clear('where');
    $query->where('type=4');
    $count = $db->setQuery($query)->loadResult();
    if ($count == 0) {
      $query->clear('select')
              ->clear('where');
      $query->select($db->quoteName('value'))
              ->from($db->quoteName('#__template_styles', 's'))
              ->where('name=s.id')
              ->where('type=1')
              ->order('s.home');
      $defs = $db->setQuery($query)->loadResultArray();
//    $out = (string) $db->getQuery();
      $positions = array();
      foreach ($defs as $def) {
        $defArray = unserialize($def);
        $positions = array_merge($positions, $defArray);
      }
      /** verify positions against modules DB table */
      $query->clear();
      $query->select('position')
              ->from('#__modules')
              ->where('client_id = 0')
              ->where('published = 1')
              ->where('position <>' . $db->quote(''))
              ->group('position')
              ->order('position');
      $db->setQuery($query);
//  $out = $db->getQuery();
      $items = $db->loadResultArray();
      $cleanedPositions = array();
      foreach ($items as $item) {
        if (array_key_exists($item, $positions)) {
          $cleanedPositions[$item] = $positions[$item];
        }
      }
      $defs = serialize($cleanedPositions);
      $query->clear();
      $query->insert('#__jotcache_exclude')
              ->columns('name,value,type')
              ->values('1,' . $db->quote($defs) . ',4');
      if ($db->setQuery($query)->query()) {
        $message = "TABLE #__jotcache_exclude has been upgraded. Check JotCache TPL exclude definitions for correct values.";
      } else {
        JError::raiseNotice(100, $db->getErrorMsg());
      }
      return $message;
    }
  }

  /**
   * Running after install() method
   * @param string $type contains when installing string : 'install'
   * @param string $parent contains when installing string : 'JInstallerAdapterPackage'
   */
  function postflight($type, $parent) {
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $query->select('ordering')
            ->from('#__extensions')
            ->where('type =' . $db->quote('plugin'))
            ->where('folder =' . $db->quote('system'))
            ->where($db->quoteName('element') . ' <> ' . $db->quote('jotmarker'))
            ->order('ordering');
    $minOrder = ($db->setQuery($query)->loadResult()) - 1;
    $query->clear();
    $query->update($db->quoteName('#__extensions'))
            ->set("ordering=$minOrder")
            ->set('enabled=1')
            ->where($db->quoteName('element') . ' = ' . $db->quote('jotmarker'));
    if (!$db->setQuery($query)->query()) {
      JError::raiseNotice(100, $db->getErrorMsg());
    }
    $query->clear();
    $query->select('ordering')
            ->from('#__extensions')
            ->where('type =' . $db->quote('plugin'))
            ->where('folder =' . $db->quote('system'))
            ->where($db->quoteName('element') . ' <> ' . $db->quote('jotcache'))
            ->order('ordering desc');
    $maxOrder = ($db->setQuery($query)->loadResult()) + 1;
    $query->clear();
    $query->update($db->quoteName('#__extensions'))
            ->set("ordering=$maxOrder")
//            ->set('enabled=1')
            ->where($db->quoteName('element') . ' = ' . $db->quote('jotcache'));
    if (!$db->setQuery($query)->query()) {
      JError::raiseNotice(100, $db->getErrorMsg());
    }
    $query->clear();
    $query->update($db->quoteName('#__extensions'))
            ->set('enabled=1')
            ->where('type =' . $db->quote('plugin'))
            ->where('folder =' . $db->quote('jotcacheplugins'));
    if (!$db->setQuery($query)->query()) {
      JError::raiseNotice(100, $db->getErrorMsg());
    }
    $this->showPlugins();
    $this->checkPrerequisites();
  }

  function showPlugins() {
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $query->select('name,folder,enabled')
            ->from('#__extensions')
            ->where('type =' . $db->quote('plugin'))
            ->where('(folder =' . $db->quote('system') . ' OR folder =' . $db->quote('jotcacheplugins') . ')')
            ->where($db->quoteName('element') . " IN ('jotcache','jotmarker','crawler','crawlerext','recache')")
            ->order('name');
    $rows = $db->setQuery($query)->loadObjectList();
//    echo $query->__toString();
    echo '<div><h2>' . JText::_('PKG_JOTCACHE_LIST_PLUGINS') . '</h2><table class="table table-striped">';
    echo '<thead><tr><th width="150">Plugin name</th><th width="150">Type</th><th width="20">Status</th></tr></thead><tbody>';
    foreach ($rows as $row) {
      $status = '<i class="icon-unpublish"></i>';
      if ($row->enabled) {
        $status = '<i class="icon-publish"></i>';
      }
      echo '<tr><td>' . $row->name . '</td><td>' . $row->folder . '</td><td>' . $status . '</td></tr>';
    }
    echo '</tbody></table></div>';
  }

  function checkPrerequisites() {
    $page_dir = JPATH_ROOT . '/cache/page';
    if (file_exists($page_dir) && !(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')) {
      chmod($page_dir, 0755);
      $permission = fileperms($page_dir) & 0777;
      if (0755 !== $permission) {
        echo '<p style="color:red;">' . JText::sprintf('PKG_JOTCACHE_PAGE_DIR_PERMISSION_WARN', $page_dir) . "</p>";
      }
    }
    echo '<div><h2>' . JText::_('PKG_JOTCACHE_LIST_PREREQUISITES') . '</h2>';
    echo '<p>' . JText::_('PKG_JOTCACHE_LIST_PREREQUISITES_DESC') . '</p><table class="table table-striped">';
    echo '<thead><tr><th width="150">Requirement</th><th width="150">Current value</th><th width="20">Status</th></tr></thead><tbody>';
    $cacheDirPermission = fileperms(JPATH_ROOT . '/cache') & 0777;
    $cacheDirPermissionStatus = ($cacheDirPermission >= 0755) ? '<i class="icon-publish"></i>' : '<i class="icon-unpublish"></i>';
    if (file_exists($page_dir)) {
      $pageDirPermission = fileperms(JPATH_ROOT . '/cache/page') & 0777;
      $pageDirPermissionStatus = ($pageDirPermission >= 0755) ? '<i class="icon-publish"></i>' : '<i class="icon-unpublish"></i>';
    }
    $installedCurl = extension_loaded('curl');
    $installedCurlStatus = $installedCurl ? '<i class="icon-publish"></i>' : '<i class="icon-unpublish"></i>';
    echo '<tr><td>' . JText::_('PKG_JOTCACHE_CACHE_DIR_PERMISSION_DESC') . '</td><td>' . sprintf("%04o", $cacheDirPermission) . '</td><td>' . $cacheDirPermissionStatus . '</td></tr>';
    if (file_exists($page_dir)) {
      echo '<tr><td>' . JText::_('PKG_JOTCACHE_PAGE_DIR_PERMISSION_DESC') . '</td><td>' . sprintf("%04o", $pageDirPermission) . '</td><td>' . $pageDirPermissionStatus . '</td></tr>';
    }
    echo '<tr><td>' . JText::_('PKG_JOTCACHE_INSTALLED_CURL_DESC') . '</td><td>' . $installedCurl . '</td><td>' . $installedCurlStatus . '</td></tr>';
    echo '</tbody></table></div>';
  }

}
