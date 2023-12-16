function setAscTimer(callback, duration, interval = 1000) {
  var timerObj = {
    onfinish: function (callback) {
      this.finishCallback = callback;
    },
    finishCallback: function () {},
    pause: function () {
        this.timeout.forEach((id) => clearTimeout(id));
    },
    play: function () {
        //duration = this.currentI * interval - 1000;
        this.startTimer(this.currentI);
    },
    startTimer: function (startI = 0) {
        var self = this;
        for (var i = startI; i <= duration / interval; i++) {
            self.currentI = i;
            (function (i, interval) {
                self.timeout.push(setTimeout(function () {
                    callback(interval * i);
                    if (i === duration / interval) {
                        self.finishCallback();
                    }
                }, interval * i));
            })(i, interval);
        }
    },
    currentI: 0,
    timeout: []
  };
  timerObj.startTimer();
  return timerObj;
}

function setDescTimer(callback, duration, interval = 1000) {
  var timerObj = {
    onfinish: function (callback) {
      this.finishCallback = callback;
    },
    finishCallback: function () {},
    pause: function () {
        this.timeout.forEach((id) => clearTimeout(id));
    },
    play: function () {
        duration = this.currentI * interval - 1000;
        this.startTimer();
    },
    startTimer: function () {
        var self = this;
        for (var i = duration / interval; i >= 0; i--) {
            (function (i, interval) {
                self.timeout.push(setTimeout(function () {
                    self.currentI = i;
                    callback(interval * i);
                    if (i === 0) {
                        self.finishCallback();
                    }
                }, duration - interval * i));
            })(i, interval);
        }
    },
    currentI: 0,
    timeout: []
  };
  timerObj.startTimer();
  return timerObj;
}
