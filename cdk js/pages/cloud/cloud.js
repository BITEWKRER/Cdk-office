var app = getApp()
Page({

    /**
     * 页面的初始数据
     */
    data: {
        searchFiles: [],
        searchFilesLength: 0,
        recentFiles: [],
        recentFilesLength: 0,
        hasInfo: false,
        tmp: null,
        tipType: '',
        info: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        var that = this;

        // 云端查看文件
        wx.request({
            url: 'https://renovation.12zw.club/getDocx',
            data: {
                openid: app.globalData.openid
            },
            success(res) {
                // console.log(res.data)
                that.setData({
                    recentFiles: res.data,
                    recentFilesLength: res.data.length
                })
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
    getmore: function(e) {
        const basepath = `${wx.env.USER_DATA_PATH}`
        var name = e.target.dataset.name;
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
    getChange: function(e) {
        var that = this
        var tmpList = []
        if (e.detail.value.length != 0) {
            for (var i = 0; i < that.data.recentFiles.length; i++) {
                tmp = that.data.recentFiles[i].match(e.detail.value);
                if (tmp != null) {
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
    choose: function(name) {
        var that = this
        // console.log(that.getname(name))
        wx.showActionSheet({
            itemList: ['查看文件', '修改文件', '下载文件', '删除文件', '分享给好友'],
            success(res) {
                switch (res.tapIndex) {
                    case 0:
                        // console.log(that.gettype(name))
                        that.download(that.getname(name), that.gettype(name));
                        break;
                    case 1:
                        var type = '';
                        wx.request({
                            url: 'https://renovation.12zw.club/getFileType',
                            data: {
                                name: that.getname(name),
                                openid: app.globalData.openid,
                            },
                            success(res) {
                                var data = JSON.parse(res.data.content);
                                switch (res.data.type) {
                                    case 0:
                                        app.globalData.name = data.name;
                                        app.globalData.who = data.who;
                                        app.globalData.department = data.department;
                                        app.globalData.content = JSON.parse(data.content);
                                        break;
                                    case 1:
                                        app.globalData.content = JSON.parse(data.content)
                                        app.globalData.name = data.name
                                        break;
                                    case 2:
                                        app.globalData.name = data.name
                                        app.globalData.content = data.content
                                        break;
                                    case 3:
                                        app.globalData.content = data.content
                                        app.globalData.name = data.name
                                        break;
                                    case 4:
                                        break;
                                }
                                that.rendering(res.data.type)
                            },
                            fail() {

                            }
                        });

                        break;
                    case 2:
                        that.downloadSave(that.getname(name), that.gettype(name))
                        break;
                    case 3:
                        wx.request({
                            url: 'https://renovation.12zw.club/deleteDocx',
                            data: {
                                name: name,
                                openid: app.globalData.openid,
                            },
                            success() {
                                that.onLoad();
                            },
                            fail(res) {
                                // console.log(res.data)
                                that.setData({
                                    info: '删除失败，请检查网络！',
                                    tipType: 'error'
                                })
                            }
                        });
                        break;
                    case 4:
                        wx.setClipboardData({
                            data: '好友分享给你了一个文件，请在微信搜索应用cdk office进行文件查看' + 'https://renovation.12zw.club/downloadWord?name=' + that.getname(name) + '&openid=' + app.globalData.openid,
                            success(res) {
                                that.setData({
                                    info: '分享链接已复制到剪贴板，请点击右上角圆点隐藏应用，并粘贴发送给好友即可查看或下载文件！',
                                    tipType: 'success'
                                })
                            },
                            fail(res) {
                                that.setData({
                                    info: '分享失败',
                                    tipType: 'error'
                                })
                            }
                        })
                        break;
                    default:
                        break;
                }
            },
            fail(res) {}
        });
    },
    rendering(type) {
        switch (type) {
            case 0:
                wx.redirectTo({
                    url: '../../pages/accounting/accounting',
                })
                break;
            case 1:
                wx.redirectTo({
                    url: '../../pages/selfBill/selfBill',
                })
                break;
            case 2:
                wx.redirectTo({
                    url: '../../pages/editor/editor',
                })
                break;
            case 3:
                wx.redirectTo({
                    url: '../../pages/resume/resume',
                })
                break;
            case 4:
                wx.showToast({
                    title: '暂不支持此模板的修改,如需其他要求，请联系开发者！',
                    icon: 'none'
                })
                break;
        }
    },
    download(name, fileTpye) {
        wx.downloadFile({
            url: 'https://renovation.12zw.club/downloadWord?name=' + name + '&openid=' + app.globalData.openid,
            success(res) {
                wx.openDocument({
                    fileType: 'docx',
                    filePath: res.tempFilePath,
                })
                // var fm = wx.getFileSystemManager()
                // fm.rename({
                //     oldPath: res.tempFilePath,
                //     newPath: `${wx.env.USER_DATA_PATH}/${name}`,
                //     success: function(res) {
                //         console.log()
                //         wx.openDocument({
                //             fileType: fileTpye,
                //             filePath: `${wx.env.USER_DATA_PATH}/${name}`,
                //             success() {},
                //             fail(res) {}
                //         })
                //     },
                //     fail: function(res) {
                //         console.log(res)
                //     }
                // })
            }
        })
    },
    downloadSave(name, filetype) {
        var that = this;
        wx.downloadFile({
            url: 'https://renovation.12zw.club/downloadWord?name=' + name + '&openid=' + app.globalData.openid,
            success(res) {
                wx.saveFile({
                    tempFilePath: res.tempFilePath,
                    filePath: `${wx.env.USER_DATA_PATH}/${name + '.' + filetype}`,
                    success(res) {
                        that.setData({
                            info: '下载成功',
                            tipType: 'success'
                        })
                        // var fm = wx.getFileSystemManager()
                        // var filepath = res.savedFilePath;
                        // fm.rename({
                        //     oldPath: filepath,
                        //     newPath: `${wx.env.USER_DATA_PATH}/${name + '.' + filetype}`,
                        //     success: function(res) {
                        //         that.setData({
                        //             info: '下载成功',
                        //             tipType: 'success'
                        //         })
                        //     },
                        //     fail: function(res) {
                        //         that.setData({
                        //             info: res.errMsg,
                        //             tipType: 'error'
                        //         })
                        //     }
                        // })
                    },
                    fail(res) {
                        that.setData({
                            info: res.errMsg,
                            tipType: 'error'
                        })
                    }
                })
            },
            fail(res) {
                that.setData({
                    info: '下载失败',
                    tipType: 'error'
                })
            }
        })
    },
    gettype(name) {
        var fileTpye = '';
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
            case 'docx':
                fileTpye = 'docx';
                break;
        }
        return fileTpye;
    },
    getname(name) {
        var type = this.gettype(name);
        if (type.length == 3) {
            name = name.substring(0, name.length - 4)
        } else {
            name = name.substring(0, name.length - 5)
        }
        return name;
    }
})