<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\XHProf;
use Xhgui\Profiler\ProfilingFlags;

/**
 * @requires extension xhprof
 */
class XHProfTest extends TestCase
{
    public function setUp()
    {
        $this->profiler = new XHProf();
    }

    public function testDefaults()
    {
        $data = $this->runProfiler();
        $data = $this->filterData($data);

        $expected = array(
            'main()==>Xhgui\Profiler\Profilers\XHProf::disable',
            'main()',
        );

        $this->assertSame($expected, array_keys($data));
    }

    public function testNoFlags()
    {
        $flags = array(
            ProfilingFlags::NO_BUILTINS,
        );
        $data = $this->runProfiler($flags);
        $data = $this->filterData($data);

        $expected = array(
            'main()==>Xhgui\Profiler\Profilers\XHProf::disable',
            'main()',
        );
        $this->assertSame($expected, array_keys($data));
    }

    private function filterData($data)
    {
        // 'Xhgui\Profiler\Profilers\XHProf::disable==>xhprof_disable sometimes is in profiling, and sometimes is not
        // this varies with php version and used flags.
        unset($data['Xhgui\Profiler\Profilers\XHProf::disable==>xhprof_disable']);

        return $data;
    }
}
