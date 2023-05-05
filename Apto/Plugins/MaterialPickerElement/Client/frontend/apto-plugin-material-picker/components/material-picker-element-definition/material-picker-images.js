import Reflexion from '../../assets/img/reflexion.png';
import Transmission from '../../assets/img/transmission.png';
import Absorption from '../../assets/img/absorption.png';
import MoreInfo from '../../assets/img/btnTooltipMoreInfo.png';

export default (APTO_DIST_PATH_URL) => {
    return {
        reflexion: APTO_DIST_PATH_URL + Reflexion,
        transmission: APTO_DIST_PATH_URL + Transmission,
        absorption: APTO_DIST_PATH_URL + Absorption,
        moreInfo: APTO_DIST_PATH_URL + MoreInfo
    }
}
