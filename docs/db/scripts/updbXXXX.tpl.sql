INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/skeleton/docs/db/scripts/updbXXXX.tpl.sql $'),'/',-1),
    'trunk',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 3057 $',locate(':','$Revision: 3057 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2012-01-31 15:18:06 +0100 (уто, 31 јан 2012) $',locate(':','$Date: 2012-01-31 15:18:06 +0100 (уто, 31 јан 2012) $')+1)),
    now());
