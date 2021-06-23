const app = getApp();

App({
    onLaunch: function() {
        wx.login({
            success: res => {
                if (res.code) {
                    wx.request({
                        url: 'https://renovation.12zw.club/getOpenID',
                        header: {
                            'Content-Type': 'application/json'
                        },
                        data: {
                            js_code: res.code,
                        },
                        success: function(res) {
                            wx.setStorage({
                                key: 'openid',
                                data: res.data,
                            });
                        }
                    });
                } else {
                    console.log(res.errMsg)
                }
            }
        });
        // 获取用户信息
        wx.getSetting({
            success: res => {
                if (res.authSetting['scope.userInfo']) {
                    // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
                    wx.getUserInfo({
                        success: res => {
                            // 可以将 res 发送给后台解码出 unionId
                            this.globalData.userInfo = res.userInfo

                            // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
                            // 所以此处加入 callback 以防止这种情况
                            if (this.userInfoReadyCallback) {
                                this.userInfoReadyCallback(res)
                            }
                        }
                    })
                }
            }
        })
    },
    globalData: {
        userInfo: null,
        List: null,
        renovationName: '',
        sum: '',
        customID: 9,
        openid: '',
        content: '',
    }
})