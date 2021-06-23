var app = getApp()
Page({

    /**
     * 页面的初始数据
     */
    data: {
        isNotComfirmed: false,
        searchFiles: [],
        searchFilesLength: 0,
        recentFiles: [],
        recentFilesLength: 0,
        totalList: [],
        hasInfo: false
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        var that = this;
        wx.getStorage({
            key: 'recent',
            success: function(res) {
                that.setData({
                    recentFiles: res.data,
                    recentFilesLength: res.data.length,
                    searchFilesLength: this.data.searchFiles.length,
                })
            },
        })

        var list = []

        let fm = wx.getFileSystemManager()
        const basepath = `${wx.env.USER_DATA_PATH}`
        fm.readdir({
            dirPath: basepath, /// 获取文件列表
            success(res) {
                for (var i = 0, j = 0; i < res.files.length; i++) {
                    if (res.files[i] == "miniprogramLog") {
                        continue
                    } else if (res.files[i] == ".nomedia") {
                        continue
                    } else {

                        list[j] = res.files[i]
                        j++;
                    }
                }
                that.setData({
                    totalList: list,
                })
                that.getcloud();
            },
            fail(err) { },
            complete() { }
        })

    },
    getcloud(){
        var that = this;
        var tmpList = [];
        
        for(var i =0;i<that.data.totalList.length;i++){
            tmpList.push(that.data.totalList[i])
        }
        wx.request({
            url: 'https://renovation.12zw.club/getDocx',
            data: {
                openid: app.globalData.openid
            },
            success(res) {
                for(var i = 0;i<res.data.length;i++){
                    tmpList.push(res.data[i])
                }
                that.setData({
                    totalList:tmpList
                })
                console.log(that.data.totalList)
            },
            fail(res) {
                // console.log(res)
            }
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
    search(data, value) {

    },
    getmore: function(e) {
        wx.showActionSheet({
            itemList: ['查看文件', '修改文件', '发送给好友', '删除文件'],
            success(res) {
                switch (res.tapIndex) {
                    case 0:
                        break;
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    default:
                        break;
                }
            },
            fail(res) {
                wx.showToast({
                    title: '操作失败'
                })
            }
        });
    },
    getChange: function(e) {
        if (e.detail.value.length != 0) {
            this.setData({
                hasInfo: true
            });
        } else {
            this.setData({
                hasInfo: false
            });
        }
    }
})