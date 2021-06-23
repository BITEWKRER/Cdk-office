//index.js
//获取应用实例
const app = getApp()

Page({
    data: {
        stencils: [{
                id: 2,
                name: "装修模版"
            },
            {
                id: 4,
                name: "实习简历模版"
            }, {
                id: 5,
                name: "其他模版"
            }
        ],
        tools: [{
            id: 1,
            name: "会计对账"
        }, {
            id: 3,
            name: "月记账"
        }],
        tipType: '',
        info: ''
    },
    onShow: function() {
        wx.getClipboardData({
            success(res) {
                var url = res.data.substring(36, res.data.length)
                if (url.startsWith("https://renovation.12zw.club/downloadWord")) {
                    wx.getStorage({
                        key: 'openid',
                        success: function(res) {
                            var openid = url.substring(url.length - 28, url.length);

                            if (res.data != openid) {
                                wx.setClipboardData({
                                    data: ' ',
                                });
                                var name = '好友分享的文件';
                                var tmp = '';
                                for (var i = 0; i < url.length; i++) {
                                    if (url[i] == '=') {
                                        i++;
                                        var j = i;
                                        while (url[j] != '&') {
                                            tmp += url[j];
                                            j++;
                                        }
                                        break;
                                    }
                                }
                                name = tmp;
                                wx.showModal({
                                    title: '文件分享',
                                    content: '好友给你分享了文件，是否查看或保存？(若出现显示空白的情况，请点击预览窗口的右上方，使用其他应用打开此文件，可能会出现拉伸情况，电脑显示则可以正常显示。)',
                                    confirmText: '查看文件',
                                    cancelText: '保存文件',
                                    success: (res) => {

                                        if (res.confirm) {
                                            wx.downloadFile({
                                                url: url,
                                                success(res) {
                                                    wx.openDocument({
                                                        filePath: res.tempFilePath,
                                                        fileType: 'docx',
                                                    })
                                                },
                                                fail(res) {
                                                    wx.showToast({
                                                        title: '文件查看失败，请重试！',
                                                        icon: 'none'
                                                    })
                                                }
                                            })
                                        } else if (res.cancel) {
                                            console.log(name);
                                            wx.downloadFile({
                                                url: url,
                                                success(res) {
                                                    wx.saveFile({
                                                        tempFilePath: res.tempFilePath,
                                                        filePath: `${wx.env.USER_DATA_PATH}/${name + '.docx'}`,
                                                        success(res) {
                                                            wx.showToast({
                                                                title: '本地保存成功！',
                                                            })
                                                        },
                                                        fail(res) {
                                                            wx.showToast({
                                                                title: '本地保存成功，请重试！',
                                                                icon: 'none'
                                                            })
                                                        }
                                                    })
                                                },
                                                fail(res) {
                                                    wx.showToast({
                                                        title: '文件下载失败，请重试！',
                                                        icon: 'none'
                                                    })
                                                }
                                            })
                                        }
                                    }
                                })
                            }
                        },
                    })
                }
            }
        });
    },
    onLoad: function() {

        wx.getStorage({
            key: 'openid',
            success: function(res) {
                app.globalData.openid = res.data
            },
        })


        // 用户版本更新
        if (wx.canIUse("getUpdateManager")) {
            let updateManager = wx.getUpdateManager();
            updateManager.onCheckForUpdate((res) => {
                // 请求完新版本信息的回调
                // console.log(res.hasUpdate);
            })
            updateManager.onUpdateReady(() => {
                wx.showModal({
                    title: '更新提示',
                    content: '新版本已经准备好，是否重启应用？',
                    success: (res) => {
                        if (res.confirm) {
                            // 新的版本已经下载好，调用 applyUpdate 应用新版本并重启
                            updateManager.applyUpdate();
                        } else if (res.cancel) {
                            return false;
                        }
                    }
                })
            })
            updateManager.onUpdateFailed(() => {
                // 新的版本下载失败
                wx.hideLoading();
                wx.showModal({
                    title: '升级失败',
                    content: '新版本下载失败，请检查网络！',
                    showCancel: false
                });
            });
        }

        if (app.globalData.userInfo) {
            this.setData({
                userInfo: app.globalData.userInfo,
                hasUserInfo: true
            })
        } else if (this.data.canIUse) {
            // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
            // 所以此处加入 callback 以防止这种情况
            app.userInfoReadyCallback = res => {
                this.setData({
                    userInfo: res.userInfo,
                    hasUserInfo: true
                })
            }
        } else {
            // 在没有 open-type=getUserInfo 版本的兼容处理
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
        // console.log(e)
        app.globalData.userInfo = e.detail.userInfo
        this.setData({
            userInfo: e.detail.userInfo,
            hasUserInfo: true
        })
    },
    gotoSearch: function(e) {
        wx.navigateTo({
            url: '../search/search',
        })
    },
    gotoLocal: function(e) {
        wx.navigateTo({
            url: '../local/local',
        })
    },
    gotoCloud: function(e) {
        wx.navigateTo({
            url: '../cloud/cloud',
        })
    },
    inputFiles: function(e) {
        var that = this;
        wx.chooseMessageFile({
            count: 10,
            type: 'file',
            success(res) {
                var fm = wx.getFileSystemManager()
                var files = res.tempFiles;

                for (var i = 0; i < files.length; i++) {
                    // var name = files[i].name;
                    // fm.rename({
                    //     oldPath: files[i].path,
                    //     newPath: `${wx.env.USER_DATA_PATH}/${files[i].name}`,
                    //     success: function(res) {
                    //         // console.log(`${wx.env.USER_DATA_PATH}/${name}`)
                    //         that.setData({
                    //             info: '导入成功，请到本地文件查看',
                    //             tipType:'success'
                    //         })
                    //     },
                    //     fail: function(res) {
                    //         that.setData({
                    //             info: '导入失败，请检查网络',
                    //             tipType: 'error'
                    //         })
                    //     }
                    // })

                    wx.saveFile({
                        tempFilePath: files[i].path,
                        filePath: `${wx.env.USER_DATA_PATH}/${files[i].name}`,
                        success(res) {
                            that.setData({
                                info: '导入成功，请到本地文件查看',
                                tipType: 'success'
                            })

                        },
                        fail(res) {
                            that.setData({
                                info: '导入失败' + res.errMsg,
                                tipType: 'error'
                            })
                        }
                    })
                }

            }
        })
    },
    selectStencil: function(e) {

        switch (e.target.dataset.stencil) {
            case 0:
                //0新建文档
                wx.navigateTo({
                    url: '../editor/editor',
                })
                break;
            case 1:
                //对账单模版
                wx.navigateTo({
                    url: '../accounting/accounting',
                })
                break;
            case 2:
                //装修模版
                wx.navigateTo({
                    url: '../renovation/renovation',
                })
                break;
            case 3:
                //月账单模版
                wx.navigateTo({
                    url: '../selfBill/selfBill',
                })
                break;
            case 4:
                //简历模板
                wx.navigateTo({
                    url: '../resume/resume',
                })
                break;
            case 5:
                //其他模版
                wx.showToast({
                    title: '敬请期待 :)',
                    icon: 'none'
                })
                // wx.navigateTo({
                //   url: '../resume/resume',
                // })
                break;
            default:
                break;
        }
    }
})