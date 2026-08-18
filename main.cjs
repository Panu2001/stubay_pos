const { app, BrowserWindow } = require('electron');
const { spawn } = require('child_process');
const path = require('path');
const net = require('net');

let mainWindow;
let phpServer;
let queueWorker;

const isDev = !app.isPackaged;
const port = 8000;

function checkServer(port, callback) {
    const client = new net.Socket();
    client.once('connect', () => {
        client.destroy();
        callback(true);
    });
    client.once('error', (err) => {
        client.destroy();
        callback(false);
    });
    client.connect(port, '127.0.0.1');
}

function waitForServer(port, callback) {
    const interval = setInterval(() => {
        checkServer(port, (isUp) => {
            if (isUp) {
                clearInterval(interval);
                callback();
            }
        });
    }, 500);
}

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1200,
        height: 800,
        show: false,
        autoHideMenuBar: true,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });

    waitForServer(port, () => {
        mainWindow.loadURL(`http://127.0.0.1:${port}`);
        mainWindow.once('ready-to-show', () => {
            mainWindow.show();
        });
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

function startPhpServer() {
    let phpPath = path.join(__dirname, 'bin', 'php', 'php.exe');
    let artisanPath = path.join(__dirname, 'artisan');
    let cwdPath = __dirname;
    
    if (!isDev) {
        // In production without asar, files are in resources/app
        phpPath = path.join(process.resourcesPath, 'bin', 'php', 'php.exe');
        cwdPath = path.join(process.resourcesPath, 'app');
        artisanPath = path.join(cwdPath, 'artisan');
    }

    phpServer = spawn(phpPath, ['artisan', 'serve', '--port=' + port], {
        cwd: cwdPath,
        env: {
            ...process.env,
            DB_CONNECTION: 'sqlite',
            DB_DATABASE: path.join(cwdPath, 'database', 'database.sqlite')
        }
    });

    phpServer.stdout.on('data', (data) => console.log(`PHP: ${data}`));
    phpServer.stderr.on('data', (data) => {
        console.error(`PHP Error: ${data}`);
    });

    // Also start the queue worker
    queueWorker = spawn(phpPath, ['artisan', 'queue:work', '--tries=3', '--timeout=90'], {
        cwd: cwdPath,
        env: {
            ...process.env,
            DB_CONNECTION: 'sqlite',
            DB_DATABASE: path.join(cwdPath, 'database', 'database.sqlite')
        }
    });

    queueWorker.stdout.on('data', (data) => {
        console.log(`Queue: ${data}`);
    });
}

app.on('ready', () => {
    startPhpServer();
    createWindow();
});

app.on('window-all-closed', () => {
    app.quit();
});

app.on('quit', () => {
    if (phpServer) {
        phpServer.kill();
    }
    if (queueWorker) {
        queueWorker.kill();
    }
    app.quit();
});
