const cron = require('node-cron');
const RedisClient = require('./redis.js');
const addSendNotifyMailTaskToQueue = require('./sendNotifyMail.js');

cron.schedule('20 8 * * *', async () => {
    try {
        await addSendNotifyMailTaskToQueue(RedisClient);
        console.log('Task to send notification mail finished');
    } catch (e) {
        console.error(e);
    }
});
