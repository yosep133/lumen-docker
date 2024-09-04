const options = {
    folderPath: './src/logs/',
    dateBasedFileNaming: true,
    fileNamePrefix: 'DailyLogs_',
    fileNameExtension: '.log',    
    dateFormat: 'YYYY_MM_D',
    timeFormat: 'h:mm:ss A',
  }

log.SetUserOptions(options); 