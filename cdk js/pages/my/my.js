const app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        total: 50,
        last: '0KB',
        percent: 0,
        isvip: false,
        userInfo: {},
        hasUserInfo: false,
        canIUse: wx.canIUse('button.open-type.getUserInfo')
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        wx.request({
            url: 'https://renovation.12zw.club/isAdd',
            data:{
                openid:app.globalData.openid
            },success(res){
            },fail(res){
            }
        })
        var that = this;
        wx.request({
            url: 'https://renovation.12zw.club/isVip',
            data:{
                openid:app.globalData.openid
            },
            success(res) {
                that.setData({
                    isvip: res.data.vip
                })
            }
        })

        wx.request({
            url: 'https://renovation.12zw.club/getCloudSize',
            data: {
                openid: app.globalData.openid
            },
            success(res) {
                if (res.data.size > 1024){
                    var size = Math.ceil(res.data.size / 1024)
                    that.setData({
                        last: size + 'MB',
                        percent: Math.ceil((size / 50) * 100)
                    })
                }else{
                    that.setData({
                        last: res.data.size + 'KB',
                        percent: 1
                    })
                }
            }
        })
        if (app.globalData.userInfo) {
            this.setData({
                userInfo: app.globalData.userInfo,
                hasUserInfo: true
            })
        } else if (this.data.canIUse) {
            app.userInfoReadyCallback = res => {
                this.setData({
                    userInfo: res.userInfo,
                    hasUserInfo: true
                })
            }
        } else {
            wx.getUserInfo({
                success: res => {
                    app.globalData.userInfo = res.userInfo
                    this.setData({
                        userInfo: res.userInfo,
                        hasUserInfo: true
                    })
                }
            })
        }
    },
    getUserInfo: function(e) {
        console.log(e)
        app.globalData.userInfo = e.detail.userInfo
        this.setData({
            userInfo: e.detail.userInfo,
            hasUserInfo: true
        })
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function() {

    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide: function() {

    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function() {

    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh: function() {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom: function() {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function() {

    },
    buy() {
       wx.navigateTo({
           url: '../../pages/vip/vip',
       })
    }
})