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
        hasInfo: false,
        info: '',
        tipType: '',
        fm: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        var that = this;
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
                    fm: fm,
                    recentFiles: list,
                    recentFilesLength: list.length
                })
            },
            fail(err) {},
            complete() {}
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
    getmore: function(e) {
        const basepath = `${wx.env.USER_DATA_PATH}`
        var name = e.target.dataset.name;
        console.log(name);
        var that = this;
        switch (e.target.dataset.id) {
            case 'search':
                that.choose(name);
                break;
            case 'recent':
                that.choose(name);
                break;
        }
    },
    getChange: function (e) {
        var that = this
        var tmpList = []
        var tmp = '';
        if (e.detail.value.length != 0) {
            for (var i = 0; i < that.data.recentFiles.length; i++) {
                tmp = that.data.recentFiles[i].match(e.detail.value);
                if(tmp != null){
                    tmpList.push(tmp.input)
                }    
            }
            that.setData({
                searchFiles: Array.from(new Set(tmpList)),
                searchFilesLength: Array.from(new Set(tmpList)).length,
                hasInfo: true
            });
        } else {
            that.setData({
                hasInfo: false
            });
        }
    },
    choose(name) {
        var that = this
        wx.showActionSheet({
            itemList: ['查看文件','删除文件'],
            success(res) {
                switch (res.tapIndex) {
                    case 0:
                        var fileTpye = 'docx';
                        var type = name.substring(name.length - 3, name.length);
                        switch (type) {
                            case 'doc':
                                fileTpye = 'doc';
                                break;
                            case 'xls':
                                fileTpye = 'xls';
                                break;
                            case 'pdf':
                                fileTpye = 'pdf';
                                break;
                            case 'ppt':
                                fileTpye = 'ppt';
                                break;
                        }
                        type = name.substring(name.length - 4, name.length);
                        switch (type) {
                            case 'xlsx':
                                fileTpye = 'xlsx';
                                break;
                            case 'pptx':
                                fileTpye = 'pptx';
                                break;
                        }
                        wx.openDocument({
                            fileType: fileTpye,
                            filePath: `${wx.env.USER_DATA_PATH}/${name}`,
                            success() {},
                            fail(res) {}
                        })
                        break;
                    case 1:
                        // 云端查看文件
                        wx.request({
                            url: 'https://renovation.12zw.club/getDocx',
                            data: {
                                openid: app.globalData.openid
                            },
                            success(res) {
                                for (var i = 0; i < res.data.length; i++) {
                                    if (name == res.data[i]) {
                                        wx.showModal({
                                            title: '提示',
                                            content: '是否同时删除云文件？',
                                            cancelText:'否',
                                            confirmText:'是',
                                            success: (res) => {
                                                if (res.confirm) {
                                                    wx.request({
                                                        url: 'https://renovation.12zw.club/deleteDocx',
                                                        data: {
                                                            name: name,
                                                            openid: app.globalData.openid,
                                                        },
                                                        success() {
                                                            wx.showToast({
                                                                title: '云文件删除成功！',
                                                                icon: 'none'
                                                            })
                                                        },
                                                        fail() {
                                                            wx.showToast({
                                                                title: '删除失败，请检查网络！',
                                                                icon: 'none'
                                                            })
                                                        }
                                                    });
                                                } else if (res.cancel) {
                                                    return false;
                                                }
                                            }
                                        })
                                    }
                                }
                            },
                            fail(res) {
                                // console.log(res)
                            }
                        })

                        that.data.fm.unlink({
                            filePath: `${wx.env.USER_DATA_PATH}/${name}`,
                            success() {
                                that.onLoad()
                            },
                            fail(res) {
                                // console.log(res)
                            }
                        });
                        break;
                    default:
                        break;
                }
            },
            fail(res) {}
        });
    }
})